<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Utils;

use Ximdex\Runtime\App;
use Ximdex\XML\Base;

define('MSG_TYPE_ERROR', 0);
define('MSG_TYPE_WARNING', 1);
define('MSG_TYPE_NOTICE', 2);

class Messages
{
    /**
     * @var array
     */
    var $messages = [];
    
    /**
     * @var array
     */
    var $_validTypes = [MSG_TYPE_ERROR, MSG_TYPE_WARNING, MSG_TYPE_NOTICE];
    
    /**
     * @var string
     */
    var $displayEncoding;

    public function __construct()
    {
        $this->messages = [];
        $this->displayEncoding = App::getValue('displayEncoding');
    }

    public function add(string $message, int $type) : void
    {
        if (in_array($type, $this->_validTypes) && ! $this->searchMessageText($message, $type)) {
            $message =  Base::recodeSrc($message, $this->displayEncoding);
            $this->messages[] = ['message' => $message, 'type' => $type];
        }
    }

    public function count(int $type = null) : int
    {
        if (is_null($type) || (! is_null($type) && ! in_array($type, $this->_validTypes))) {
            return count($this->messages);
        }
        $totalMessagesByType = 0;
        foreach ($this->messages as $message) {
            if ($message['type'] == $type) {
                $totalMessagesByType++;
            }
        }
        return $totalMessagesByType;
    }

    public function displayRaw(string $type = null, bool $postClean = false) : void
    {
        if (! defined("CLI_MODE") || ! CLI_MODE) {
            return;
        }
        foreach ($this->messages as $message) {
            if ($type !== null) {
                if ($message['type'] == $type) {
                    echo $message['message'] . "\n";
                }
            } else {
                echo $message['message'] . "\n";
            }
        }
        if ($postClean) {
            $this->messages = [];
        }
    }

    public function getXml(string $status) : string
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

    public function getRaw(int $messageType = null) : string
    {
        $messageString = '';
        foreach ($this->messages as $message) {
            if (! is_null($messageType)) {
                if ($messageType == $message['type']) {
                    $messageString =  $message['message'] . "\n";
                }
            } else {
                $messageString =  $message['message'] . "\n";
            }
        }
        return $messageString;
    }

    public function getHtml(string $header = 'Messages', string $type = 'ALL') : void
    {
        if (! count($this->messages)) {
            return;
        }
?>
        <tr>
            <td class='filaoscuranegritac'><?php echo htmlentities($header); ?>:</td>
        </tr>
<?php
        foreach ($this->messages as $message) {
            if ($type != 'ALL' && $message['type'] != $type) {
                continue;
            }
?>
            <tr>
                <td class="filaclara" style="line-height: 15px;"><?php echo htmlentities($message['message']); ?></td>
            </tr>

<?php
        }
    }

    public function logMessagesToErrorLog() : void
    {
        ob_start();
        $log = ob_get_clean();
        error_log($log);
    }

    /**
     * Añade a este objeto de mensajes los mensajes provenientes de otro objeto
     * 
     * @param array $messages
     */
    public function mergeMessages(Messages $messages = null) : void
    {
        if ($messages !== null and is_array($messages->messages)) {
            $this->messages = array_merge($this->messages, $messages->messages);
        }
    }

    /**
     * Return the related message from the last PHP error, or null if there is not
     * 
     * @return string|null
     */
    public static function error_message(string $replace = null) : ?string
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
    
    private function searchMessageText(string $messageText, int $type) : bool
    {
        foreach ($this->messages as $message) {
            if ($message['message'] == $messageText && $message['type'] == $type) {
                return true;
            }
        }
        return false;
    }
}
