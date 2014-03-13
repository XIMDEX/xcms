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



ModulesManager::file('/inc/metadata/MetadataManager.class.php');
ModulesManager::file('/inc/parsers/ParsingRng.class.php');
ModulesManager::file('/actions/manageproperties/inc/LanguageProperty.class.php');



/**
 * Manage metadata action. 
 *
 */
class Action_managemetadata extends ActionAbstract {

	/**
	 * Main function
	 *
	 * Load the manage metadata form.
	 *
	 * Request params:
	 * 
	 * * nodeid
	 * 
	 */
	public function index() {

		//Load css and js resources for action form.
		$this->addCss('/actions/managemetadata/resources/css/style.css');
		$this->addJs('/actions/managemetadata/resources/js/index.js');

		$nodeId = $this->request->getParam('nodeid');

		$node = new Node($nodeId);
		$info = $node->loadData();
		$values["nodename"] = $info["name"];
		$values["nodeversion"] = $info["version"].".".$info["subversion"];
		$values["nodepath"] = $info["path"];
		$values["typename"] = $info["typename"];

		// Getting languages
		if ($info["typename"] == "XmlDocument") {
			$structuredDodument = new StructuredDocument($nodeId);
			$idLanguage = $structuredDodument->get('IdLanguage');
			$l = new Language($idLanguage);
			$lngs[] = array(
				'IdLanguage' => $idLanguage,
				'Name' => $l->get('Name'),
				'Checked' => 1
			);
		}
		else {
			$lp = new LanguageProperty($nodeId);
			$lngs = $lp->getValues();
		}
		
		if ($lngs) {
			$values['default_language'] = $lngs[0]['IdLanguage'];
			$values['languages'] = $lngs;
        	$values['json_languages'] = json_encode($lngs);
		}

        $values['languages_metadata'] = array();
		$mm = new MetadataManager($nodeId);
		$metadata_nodes = $mm->getMetadataNodes();

		foreach ($metadata_nodes as $metadata_node_id) {
			$structuredDodument = new StructuredDocument($metadata_node_id);
			$idLanguage = $structuredDodument->get('IdLanguage');
			$metadata_node = new Node($metadata_node_id);
			$content = $metadata_node->getContent();
			$domDoc = new DOMDocument();
	        if ($domDoc->loadXML("<root>".$content."</root>")) {
	        	$xpathObj = new DOMXPath($domDoc);
	        	$custom_info = $xpathObj->query("//custom_info/*");
	        	if ($custom_info->length > 0) {
	        		foreach ($custom_info as $value) {
	        			$values['languages_metadata'][$idLanguage][$value->nodeName] = $value->nodeValue;
	        			// foreach ($lngs as $language) {
	        			// 	$values['languages_metadata'][$language['IdLanguage']][$value->nodeName] = $value->nodeValue;
	        			// }
	        		}
	        	}
			}
		}

		$values["elements"] = array();
		
		$nodesearch = new Node();
		$idRelaxNGNode = $mm->getMetadataSchema();
		if ($idRelaxNGNode) {
			$rngParser = new ParsingRng();
			$values['elements'] = $rngParser->buildFormElements($idRelaxNGNode, 'custom_info');
		}
		
		$values['nodeid'] = $nodeId;
		$values['go_method'] = 'save_metadata';

		$this->render($values, '', 'default-3.0.tpl');
	}





	/**
	 * Save the results from the form
	 */
	public function save_metadata() {
		$errors = array();
        $messages = "";

        // Retrieve POST values
        $nodeId = $this->request->getParam('nodeid');
        $languages_metadata = $this->request->getParam('languages_metadata');

        // Retrieve custom fields array (for one language - the rest of the languages are the same fields)
        $custom_fields = array();
        if ($languages_metadata) {
        	foreach ($languages_metadata[key($languages_metadata)] as $fieldname => $fieldvalue) {
        		$custom_fields[] = $fieldname;
        	}
        }

        error_log("****************************************");

        // Retrieve Metadata XMLs
        $mm = new MetadataManager($nodeId);
		$metadata_nodes = $mm->getMetadataNodes();
		foreach ($metadata_nodes as $metadata_node_id) {
			$metadata_node = new StructuredDocument($metadata_node_id);
			$idLanguage = $metadata_node->get('IdLanguage');
			$content = $metadata_node->getContent();
			$domDoc = new DOMDocument();
	        if ($domDoc->loadXML("<root>".$content."</root>")) {
	        	foreach ($custom_fields as $custom_field) {
	        		$custom_element = $domDoc->getElementsByTagName($custom_field)->item(0);

	        		error_log(mb_detect_encoding(utf8_encode($languages_metadata[$idLanguage][$custom_field])));

	        		$custom_element->nodeValue = $languages_metadata[$idLanguage][$custom_field];
	        		// echo $custom_element->textContent . " must be replaced by " . $languages_metadata[$idLanguage][$custom_field] . "\n";
	        	}
	        	$metadata_node_update = new Node($metadata_node_id);
	        	$string_xml = $domDoc->saveXML();
	        	$string_xml = str_replace('<?xml version="1.0"?>', '', $string_xml);
	        	$string_xml = str_replace('<root>', '', $string_xml);
	        	$string_xml = str_replace('</root>', '', $string_xml);
	        	$metadata_node_update->setContent($string_xml);
			}
		}

		error_log("****************************************");

        $values = array(
                'metadata' => array(
                ),
                'messages' => $messages,
                'errors' => $errors
        );

        $this->sendJSON($values);
		
	}



}
?>
