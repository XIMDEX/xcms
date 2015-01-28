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



if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.php');
require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');
require_once(XIMDEX_ROOT_PATH . "/inc/pipeline/PipeCacheTemplates.class.php");
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_Dext extends Abstract_View implements Interface_View {

	private $_node = null;
	private $_idSection = null;
	private $_idProject = null;
	private $_depth = null;

	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		XMD_Log::info('Starting dext transformation');
		$content = $this->retrieveContent($pointer);
		if (!$this->_setNode($idVersion))
			return NULL;

		// Get Params:
		if (!$this->_setIdSection($args))
			return NULL;
		if (!$this->_setIdProject($args))
			return NULL;
		if (!$this->_setDepth($args))
			return NULL;

		$section = new Node($this->_idSection);
		$sectionPath = $section->class->GetNodePath();

		$project = new Node($this->_idProject);
		$nodeProjectPath = $project->class->GetNodePath();

		$tplFolder = \App::getValue( "TemplatesDirName");
		$generatorCommand = \App::getValue( "AppRoot").\App::getValue( "GeneratorCommand");

		$command = $generatorCommand .
				" --template='".$sectionPath."/".$tplFolder."'" .
				" --common='".$nodeProjectPath."/".$tplFolder."'" .
				" --depth=".($this->_depth - 2) .
				" --errorout";

		$prefix = 'dextdin';
		$tmpPath = \App::getValue( "AppRoot") . \App::getValue( "TempRoot");
		$tmpfile = tempnam($tmpPath , $prefix);

		$pipe = popen("$command 2>&1 >$tmpfile", 'w');
		if (!$pipe) {
			XMD_Log::error("Generator can not be executed #1");
			FsUtils::delete($tmpfile);
		} else {
			fwrite($pipe, $content, strlen($content));
			$returnValue = pclose($pipe);
			XMD_Log::debug("Generator returned code $returnValue");

			$output = FsUtils::file_get_contents($tmpfile);
			FsUtils::delete($tmpfile);

			$ciclosDecode = \App::getValue( "UTFLevel");

			if ( $ciclosDecode >= 0 && $ciclosDecode < 5 ) {
				for($i = 0; $i < $ciclosDecode; $i++) {
				    XMD_Log::debug("UTF8 decoding cycle applied");
				    $output = utf8_decode($output);
				}
			}

		}

		if($returnValue) {
			if($returnValue == 127) {
				XMD_Log::error("Error de llamada al m�dulo dexT");
			} elseif ($returnValue == 2) {
				XMD_Log::error("Error en configuraci�n de librerias del m�dulo dexT");
			} else {
				$j = 0;
				$out = explode("\n",$output);
				while ($j<(count($out))) {
					if (strlen($out[$j]) > 0) {
						$tmp[] = $out[$j];
					}
					$j++;
				}


				$tmp[0] = preg_replace("/GENERATION ERROR -->/", "Error de Generaci�n", $tmp[0]);
				$tmp[0] = preg_replace("/SYNTAX ERROR -->/", "Error de Sintaxis en la llamada", $tmp[0]);
				$tmp[0] = preg_replace("/CONFIGURATION ERROR -->/", "Error de Configuracion", $tmp[0]);

				$tmp[1] = preg_replace("/^syntax error at/i", "Error de Sintaxis.", $tmp[1]);
				$tmp[1] = preg_replace("/^junk after document element at/i", "Caracteres detras de la etiqueta <docxap>.", $tmp[1]);
				$tmp[1] = preg_replace("/^mismatched tag at/i", "Etiqueta de apertura y de cierre no coinciden.", $tmp[1]);
				$tmp[1] = preg_replace("/^unclosed token at/i", "Etiqueta sin cerrar.", $tmp[1]);
				$tmp[1] = preg_replace("/^not well-formed \(invalid token\) at/i", "Etiqueta mal formada (elemento invalido).", $tmp[1]);
				$tmp[1] = preg_replace("/line/i", "linea", $tmp[1]);
				$tmp[1] = preg_replace("/column/i", "columna", $tmp[1]);
				$output = implode("\n",$tmp);
				XMD_Log::error("Generator returned output error $output");
			}
		}


		// Extract last line (which has template dependences info)

		$lines = explode("\n", $output);
		$lastLine = array_pop($lines);
		$output = implode($lines, "\n");
		// Insert document-xsl templates dependencies

		if($this->_node) {
			$deps = explode('-->', $lastLine);

			if (array_key_exists(1,$deps)){
				$dependences = explode(' ', trim($deps[1]));

				foreach ($dependences as $templateVersion) {
					$pipeTemplate = new PipeCacheTemplates();
					$pipeTemplate->set('NodeId', $this->_node->get('IdNode'));
					$pipeTemplate->set('DocIdVersion', $idVersion);
					$pipeTemplate->set('TemplateIdVersion', $templateVersion);
					$pipeTemplate->add();
				}
			}
		}
		return $this->storeTmpContent($output);
	}

	private function _setNode ($idVersion = NULL) {

		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error('VIEW DEXT: Se ha cargado una versi�n incorrecta (' . $idVersion . ')');
				return NULL;
			}

			$this->_node = new Node($version->get('IdNode'));
			if (!($this->_node->get('IdNode') > 0)) {
				XMD_Log::error('VIEW DEXT: El nodo que se est� intentando convertir no existe: ' . $version->get('IdNode'));
				return NULL;
			}
		}

		return true;
	}

	private function _setIdSection ($args = array()) {

		if($this->_node) {
			$this->_idSection = $this->_node->GetSection();
		} elseif (array_key_exists('SECTION', $args)) {
			$this->_idSection = $args['SECTION'];
		}

		// Check Params:
		if (!isset($this->_idSection) || !($this->_idSection > 0)) {
			XMD_Log::error('VIEW DEXT: No se ha especificado la secci�n del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

	private function _setIdProject ($args = array()) {

		if($this->_node) {
			$this->_idProject = $this->_node->GetProject();
		} elseif (array_key_exists('PROJECT', $args)) {
			$this->_idProject = $args['PROJECT'];
		}

		// Check Params:
		if (!isset($this->_idProject) || !($this->_idProject > 0)) {
			XMD_Log::error('VIEW DEXT: There is not associated project for the node ' . $args['NODENAME']);
			return NULL;
		}

		return true;
	}

	private function _setDepth ($args = array()) {

		if($this->_node) {
			$this->_depth = $this->_node->GetPublishedDepth();
		} elseif (array_key_exists('DEPTH', $args)) {
			$this->_depth = $args['DEPTH'];
		}

		// Check Param:
		if (!isset($this->_depth) || !($this->_depth > 0)) {
			XMD_Log::error('VIEW DEXT: No se ha especificado la profundidad del nodo ' . $args['NODENAME'] . ' que quiere renderizar');
			return NULL;
		}

		return true;
	}

}
?>