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

namespace Ximdex\Utils ;

use Ximdex\Runtime\App;
use Ximdex\XML\Base;

define ('MSG_TYPE_ERROR', 0);
define ('MSG_TYPE_WARNING', 1);
define ('MSG_TYPE_NOTICE', 2);

class Messages
{
    /**
     * @var array|null
     */
    var $messages = null;
    
    /**
     * @var array|null
     */
    var $_validTypes = null;
    
    /**
     * @var null
     */
    var $displayEncoding = null;

    /**
     * Messages constructor
     */
    public function __construct()
    {
        $this->_validTypes = array(MSG_TYPE_ERROR, MSG_TYPE_WARNING, MSG_TYPE_NOTICE);
        $this->messages = array();
        $this->displayEncoding = App::getValue( 'displayEncoding');
    }

    /**
     * @param $message
     * @param $type
     */
    function add($message, $type)
    {
        if (in_array($type, $this->_validTypes) && !$this->_searchMessageText($message, $type)) {
            $message =  Base::recodeSrc($message, $this->displayEncoding);
            $this->messages[] = array('message' => $message, 'type' => $type);
        }
    }

    /**
     * @param null $type
     * @return int
     */
    function count($type = null)
    {
        if (is_null($type)
            || (!is_null($type) && !in_array($type, $this->_validTypes))) {
            return count($this->messages);
        }
        $totalMessagesByType = 0;
        foreach ($this->messages as $message) {
            if ($message['type'] == $type) $totalMessagesByType++;
        }
        return $totalMessagesByType;
    }

    /**
     * @param null $type
     * @param bool $postClean
     */
    function displayRaw($type = NULL, $postClean = false) {
        if (!defined("CLI_MODE") || !CLI_MODE) {
            return;
        }
        foreach ($this->messages as $message) {
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
     * @param $status
     * @return string
     */
    function getXml($status)
    {
        $messagesText = '';
        foreach ($this->messages as $message) {
            $messagesText .= sprintf('<message type="%s">%s</message>', $message['type'], utf8_encode($message['message'])) . "\n";
        }
        $returnValue  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $returnValue .= sprintf('<messages status="%s">', $status) . "\n";
        $returnValue .= $messagesText . "\n";
        $returnValue .= '</messages>' . "\n";
        return $returnValue;
    }

    /**
     * @param null $messageType
     * @return string
     */
    function getRaw ($messageType = NULL)
    {
        $messageString = '';
        foreach ($this->messages as $message) {
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
     * @param string $header
     * @param string $type
     */
    function getHtml($header = 'Messages', $type = 'ALL')
    {
        if (!(count($this->messages) > 0)) {
            return ;
        }
?>
        <tr>
            <td class='filaoscuranegritac'><?php echo htmlentities($header); ?>:</td>
        </tr>
<?php
        foreach ($this->messages as $message) {
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

    function logMessagesToErrorLog()
    {
        ob_start();
        $log = ob_get_clean();
        error_log($log);
    }

    /**
     * @param $messageText
     * @param $type
     * @return bool
     */
    function _searchMessageText($messageText, $type)
    {
        foreach ($this->messages as $message) {
            if (($message['message'] == $messageText) && ($message['type'] == $type)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Añade a este objeto de mensajes los mensajes provenientes de otro objeto
     * 
     * @param $messages
     */
    function mergeMessages($messages)
    {
        if (isset($messages->messages) && is_array($messages->messages)) {
            $this->messages = array_merge($this->messages, $messages->messages);
        }
    }

    /**
     * Return the related message from the last PHP error, or null if there is not
     * 
     * @return string|null
     */
    public static function error_message($replace = null)
    {
        $error = error_get_last();
        if ($error) {
            $error = $error['message'];
            if ($replace) {
                $error = str_ireplace($replace, '', $error);
            }
            return $error;
        }
        return null;
    }
}