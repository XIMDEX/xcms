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




require_once(XIMDEX_ROOT_PATH . '/inc/model/Versions.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/channel.inc');
require_once(XIMDEX_ROOT_PATH . '/inc/model/Server.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/helper/String.class.php');
require_once XIMDEX_ROOT_PATH . '/inc/http/Curl.class.php';
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_PreviewInServer extends Abstract_View implements Interface_View {

	private $_node = NULL;
	private $_serverNode = NULL;
	private $_idChannel;
	
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		$content=$pointer;
		$content = $this->retrieveContent($pointer);
		if(!$this->_setNode($idVersion))
			return NULL;
		
		if(!$this->_setIdChannel($args))
			return NULL;	
			
		if(!$this->_setServerNode($args))
			return NULL;

		if (Config::getValue('PreviewInServer') == 0) {
			XMD_Log::error('PreviewInServer mode is disabled');
			return NULL;
		}

		$content = htmlspecialchars_decode(String::stripslashes($content));
		$previewServer = $this->_serverNode->class->GetPreviewServersForChannel($this->_idChannel);

		if(!$previewServer) {
			XMD_Log::error('No Preview Servers for this channel');
			return NULL;
		}

		$commandParams = array();
		$commandParams['publishedName'] = $this->_node->GetPublishedNodeName($this->_idChannel);
		$commandParams['publishedPath'] = $this->_node->GetPublishedPath();
		$commandParams['publishedBaseURL']  = $this->_serverNode->class->GetURL($previewServer);
		$commandParams['publishedURL'] = $commandParams['publishedBaseURL'] . $commandParams['publishedPath'] 
			. "/" . $commandParams['publishedName'];
		$commandParams['tmpPath'] = Config::getValue("AppRoot") . Config::getValue("TempRoot");
		$commandParams['tmpfile'] = tempnam($commandParams['tmpPath'] , $prefix = null);
		$commandParams['tmpfileName'] = basename($commandParams['tmpfile']);
		
		if (!FsUtils::file_put_contents($commandParams['tmpfile'], $content)) {
			return false;
		}
		//@chmod($commandParams['tmpfile'], 0777);
		$command = 	Config::getValue("AppRoot") . Config::getValue("SynchronizerCommand") .
					" --verbose 10 --direct --hostid " . $previewServer . " " .
					" --localbasepath " . $commandParams['tmpPath'] . " --dcommand up --dlfile " .
					$commandParams['tmpfileName'] . " --drfile " . $commandParams['publishedName'] . " " .
					" --drpath " . $commandParams['publishedPath'] . "/";
		$returnValue = null;
		$outPut = array();
		exec($command, $outPut, $returnValue);
		switch($returnValue) {
			// TODO: manage fetching errors
			case 0:
				$curl = new Curl();
				$response = $curl->get($commandParams['publishedURL']);
				XMD_Log::info('Success');
				$content = $response['data'];
				break;
			case 10:
				XMD_Log::error('Error accessing remote server');
				$content = '';
				//return '3';
				break;
			case 200:
				XMD_Log::error('Error accessing to the remote server (please, check IPs and login credentials)');
				$content = '';
				//return '4';
				break;
			default:				
				XMD_Log::error('Error de invocaci�n, comando mal formado, etc. (error desconocido)');
				$content = '';
				break;
				//return '5';
		}

		return $this->storeTmpContent($content);
	}
	
	private function _setNode ($idVersion = NULL) {
		
		if(!is_null($idVersion)) {
			$version = new Version($idVersion);
			if (!($version->get('IdVersion') > 0)) {
				XMD_Log::error('VIEW FILTERMACROSPREVIEW: Se ha cargado una versi�n incorrecta (' . $idVersion . ')');
				return NULL;
			}
			
			$this->_node = new Node($version->get('IdNode'));
			if (!($this->_node->get('IdNode') > 0)) {
				XMD_Log::error('VIEW FILTERMACROSPREVIEW: El nodo que se est� intentando convertir no existe: ' . $version->get('IdNode'));
				return NULL;
			}
		}
		
		return true;
	}
	
	private function _setIdChannel ($args = array()) {
			
		if (array_key_exists('CHANNEL', $args)) {
			$this->_idChannel = $args['CHANNEL'];
		}
		
		// Check Params:
		if (!isset($this->_idChannel) || !($this->_idChannel > 0)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: Channel not specified for node ' . $args['SERVERNODE']);
			return NULL;
		}
		
		return true;
	}

	private function _setServerNode ($args = array()) {
			
		if($this->_node) {
			$this->_serverNode = new Node($this->_node->getServer());
		} elseif (array_key_exists('SERVERNODE', $args)) {
			$this->_serverNode = new Node($args['SERVERNODE']);
		}

		// Check Params:
		if (!($this->_serverNode) || !is_object($this->_serverNode)) {
			XMD_Log::error('VIEW FILTERMACROSPREVIEW: There is no server linked to the node ' . $args['NODENAME']);
			return NULL;
		}
		
		return true;
	}
		
}
?>