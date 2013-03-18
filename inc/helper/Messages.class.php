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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
}

require_once (XIMDEX_ROOT_PATH . '/inc/persistence/Config.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/xml/XmlBase.class.php');

define ('MSG_TYPE_ERROR', 0);
define ('MSG_TYPE_WARNING', 1);
define ('MSG_TYPE_NOTICE', 2);

class Messages {
	/**
	 * 
	 * @var unknown_type
	 */
	var $messages = null;
	/**
	 * 
	 * @var unknown_type
	 */
	var $_validTypes = null;
	/**
	 * 
	 * @var unknown_type
	 */
	var $displayEncoding = null;
	/**
	 * Constructor
	 * @return unknown_type
	 */
	function Messages(){
		$this->_validTypes = array(MSG_TYPE_ERROR, MSG_TYPE_WARNING, MSG_TYPE_NOTICE);
		$this->messages = array();
		$this->displayEncoding = Config::getValue('displayEncoding');
	}
	function add($message, $type) 
	{
		if (in_array($type, $this->_validTypes) && !$this->_searchMessageText($message, $type)) {
			$message = XmlBase::recodeSrc($message, $this->displayEncoding);
			$this->messages[] = array('message' => $message, 'type' => $type);
		}
	}
	
	/**
	 * 
	 * @param $type
	 * @return unknown_type
	 */
	function count($type = null) 
	{
		if (is_null($type) 
			|| (!is_null($type) && !in_array($type, $this->_validTypes))) {
				return count($this->messages);
		}
		reset($this->messages);
		$totalMessagesByType = 0;
		while(list(, $message) = each($this->messages)) {
			if ($message['type'] == $type) $totalMessagesByType ++;
		}
		return $totalMessagesByType;
	}
	
	/**
	 * 
	 * @param $type
	 * @param $postClean
	 * @return unknown_type
	 */
	function displayRaw ($type = NULL, $postClean = false) {
		if(!defined("CLI_MODE") || !CLI_MODE) return ;
		reset($this->messages);
		while(list(, $message) = each($this->messages)) {
			if ($type !== NULL) {
				if ($message['type'] == $type) {
					echo $message['message'] . "\n";
				}
			} else {
				echo $message['message'] . "\n";
			}
		}
		if ($postClean) {
			$this->messages = array();
		}
	}
	
	/**
	 * 
	 * @param $status
	 * @return unknown_type
	 */
	function getXml ($status) {
		$messagesText = '';
		reset($this->messages);
		while(list(, $message) = each ($this->messages)) {
			$messagesText .= sprintf('<message type="%s">%s</message>', $message['type'], utf8_encode($message['message'])) . "\n";
		}
		$returnValue  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$returnValue .= sprintf('<messages status="%s">', $status) . "\n";
		$returnValue .= $messagesText . "\n";
		$returnValue .= '</messages>' . "\n";
		return $returnValue;
	}
	
	/**
	 * 
	 * @param $messageType
	 * @return unknown_type
	 */
	function getRaw ($messageType = NULL) {
		$messageString = '';
		reset($this->messages);
		while(list(, $message) = each($this->messages)) {
			if (!is_null($messageType)) {
				if ($messageType == $message['type']) {
					$messageString =  $message['message'] . "\n";
				}
			} else {
				$messageString =  $message['message'] . "\n";
			}
		}
		return $messageString;
	}
	
	/**
	 * Workaround hasta que este el mvc
	 * @param $header
	 * @param $type
	 * @return unknown_type
	 */
	function getHtml($header = 'Messages', $type = 'ALL') {
		if (!(count($this->messages) > 0)) {
			return ;
		}
	?>
        <tr>
                <td class='filaoscuranegritac'><?php echo htmlentities($header); ?>:</td>
        </tr>
<?php
        reset($this->messages);
        while(list(, $message) = each($this->messages)) {
                if (($type != 'ALL') && ($message['type'] != $type)) {
                        continue;
                }
?>
        <tr>
                <td class="filaclara" style="line-height: 15px;"><?php echo htmlentities($message['message']); ?></td>
        </tr>

<?php
        }
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function logMessagesToErrorLog() {
		ob_start();
//		var_dump($this->messages);
		$log = ob_get_clean();
		error_log($log);
	}
	
	/**
	 * 
	 * @param $messageText
	 * @param $type
	 * @return unknown_type
	 */
	function _searchMessageText($messageText, $type) {
		reset($this->messages);
		while (list(, $message) = each($this->messages)) {
			if (($message['message'] == $messageText) && ($message['type'] == $type)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Añade a este objeto de mensajes los mensajes provenientes de otro objeto
	 * @param $messages
	 * @return unknown_type
	 */
	function mergeMessages($messages) {
		$this->messages = array_merge($this->messages, $messages->messages);
	}
}
?>
