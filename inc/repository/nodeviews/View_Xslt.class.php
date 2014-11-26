<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */



ModulesManager::file('/inc/model/Versions.php');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');
ModulesManager::file('/inc/xml/XSLT.class.php');
ModulesManager::file('/xslt/functions.php', 'dexT');
ModulesManager::file('/inc/pipeline/PipeCacheTemplates.class.php');
ModulesManager::file('/inc/repository/nodeviews/Abstract_View.class.php');
ModulesManager::file('/inc/repository/nodeviews/Interface_View.class.php');


class View_Xslt extends Abstract_View {

	private $_node;
	private $_idSection;
	private $_idChannel;
	private $_idProject;

	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {


		$content = $this->retrieveContent($pointer);
		if(!$this->_setNode($idVersion))
			return NULL;

		if(!$this->_setIdChannel($args))
			return NULL;

		if(!$this->_setIdSection($args))
			return NULL;

		if(!$this->_setIdProject($args))
			return NULL;


		$ptdFolder = \App::getValue( "TemplatesDirName");

		$section = new Node($this->_idSection);
		$sectionPath = $section->class->GetNodePath();

		$docxap = $sectionPath . '/' . $ptdFolder . '/docxap.xsl';

		// Only make transformation if channel's render mode is ximdex (or null)

		if ($this->_idChannel) {
			$channel = new Channel($this->_idChannel);
			$renderMode = $channel->get('RenderMode');

			if ($renderMode == 'client') {
				$inclusionHeader = '<?xml-stylesheet type="text/xsl" href="' . $ptdFolder . '/docxap.xsl"?>';
				$xmlHeader = \App::getValue( 'EncodingTag');
				$content = str_replace($xmlHeader, $xmlHeader . $inclusionHeader, $content);

				XMD_Log::info('Render in client, return XML content + path to template');
				return $content;
			}

/*			if (is_object($this->_node)) {

				XMD_Log::info("obteniendo propiedad otf para id ".$this->_node->get('IdNode'));
				$isOTF = $this->_node->getSimpleBooleanProperty('otf');

				if ($isOTF) {
					XMD_Log::info('Render in server, return XML content');
					return $content;
				}
			}
*/
		}

		// XSLT Transformation

		XMD_Log::info('Starting xslt transformation');
		if (!file_exists($docxap)) {
			$project = new Node($this->_idProject);
			$nodeProjectPath = $project->class->GetNodePath();

			$docxap = $nodeProjectPath . '/' . $ptdFolder . '/docxap.xsl';
		}


		$xsltHandler = new XSLT();
		$xsltHandler->setXML($pointer);
		$xsltHandler->setXSL($docxap);
		$params = array('xmlcontent' => $content);
		foreach ($params as $param => $value) {
		    $xsltHandler->setParameter(array($param => $value));
		}
		
		$content = $xsltHandler->process();
		if (empty($content)) {
		    XMD_Log::error("Error in XSLT process for $docxap ");
		    return NULL;
		}

		// Tags counter

		$counter = 1;

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;

		if ($channel->get("OutputType")=="xml"){
       			if (!$domDoc->loadXML($content, LIBXML_NOERROR)) {
       				XMD_log::error($content);
				XMD_log::error('XML invalid');
				return NULL;
			}
		}
		else if ($channel->get("OutputType")=="web"){  
		      	if (!$domDoc->loadHTML($content)){
			     XMD_log::error('HTML invalid');
			    return NULL;
		    	}
                }else{
                    return $this->storeTmpContent($content);
                }
		$xpath = new DOMXPath($domDoc);

		$nodeList = $xpath->query('/html/body//*[string(text())]');

		// In non-node transform we've not got a nodeid, and it's not necessary for tag counting.
		foreach ($nodeList as $element) {
			$element->setAttributeNode(new DOMAttr('uid', (($this->_node) ? $this->_node->get('IdNode') : '00000') . ".$counter" ));
			$counter++;
		}

		if ($channel->get("OutputType")=="xml")
                       $content = $domDoc->saveXML();
        	else if ($channel->get("OutputType")=="web")
                       $content = $domDoc->saveHTML();

		return $this->storeTmpContent($content);
	}

	private function _setNode ($idVersion = NULL) {

		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error('VIEW XSLT: Se ha cargado una versi�n incorrecta (' . $idVersion . ')');
				return NULL;
			}

			$this->_node = new Node($version->get('IdNode'));
			if (!($this->_node->get('IdNode') > 0)) {
				XMD_Log::error('VIEW XSLT: El nodo que se est� intentando convertir no existe: ' . $version->get('IdNode'));
				return NULL;
			}
		}else{
        	XMD_Log::info("VIEW XSLT: Se instancia vista xslt sin idVersion");
        }

		return true;
	}

	private function _setIdChannel ($args = array()) {

		if (array_key_exists('CHANNEL', $args)) {
			$this->_idChannel = $args['CHANNEL'];
		}

		// Check Params:
		if (!isset($this->_idChannel) || !($this->_idChannel > 0)) {
			XMD_Log::error('VIEW XSLT: Node ' . $args['NODENAME'] . ' has not an associated channel');
			return NULL;
		}

		return true;
	}

	private function _setIdSection ($args = array()) {

		if($this->_node) {
			$this->_idSection = $this->_node->GetSection();
		} else {
			if (array_key_exists('SECTION', $args)) {
				$this->_idSection = $args['SECTION'];
			}

			// Check Params:
			if (!isset($this->_idSection) || !($this->_idSection > 0)) {
				XMD_Log::error('VIEW XSLT: There is not associated section for the node ' . $args['NODENAME']);
				return NULL;
			}
		}

		return true;
	}

	private function _setIdProject ($args = array()) {

		if($this->_node) {
			$this->_idProject = $this->_node->GetProject();
		} else {
			if (array_key_exists('PROJECT', $args)) {
				$this->_idProject = $args['PROJECT'];
			}

			// Check Params:
			if (!isset($this->_idProject) || !($this->_idProject > 0)) {
				XMD_Log::error('VIEW XSLT: There is not associated project for the node ' . $args['NODENAME']);
				return NULL;
			}
		}

		return true;
	}

}
?>