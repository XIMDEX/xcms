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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Utils;

require_once(XIMDEX_ROOT_PATH . XIMDEX_VENDORS . '/phpmailer/phpmailer/src/Exception.php');
require_once(XIMDEX_ROOT_PATH . XIMDEX_VENDORS . '/phpmailer/phpmailer/src/PHPMailer.php');
require_once(XIMDEX_ROOT_PATH . XIMDEX_VENDORS . '/phpmailer/phpmailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use Ximdex\Runtime\App;

// Include mail configuration.
include_once(XIMDEX_ROOT_PATH . "/conf/mail.php");

// Default values.

class Mail extends PHPMailer
{
    function __construct()
    {
        $this->From = FROM;
        $this->FromName = FROM_NAME;
        $this->Sender = FROM;
        if (defined('SMTP_AUTH') && SMTP_AUTH == true) {
            $this->Mailer = "smtp";
            $this->Host = SMTP_SERVER;
            $this->SMTPAuth = true;
            $this->Username = AUTH_USERNAME;
            $this->Password = AUTH_PASSWD;
            $this->Host = AUTH_HOST;
        }
    }

    function setFrom($address, $name = '', $auto = true)
    {
        parent::setFrom($address, $name, $auto);
        $this->From = $address;
        $this->FromName = $name;
        $this->Sender = $this->From;
    }

    function error_handler($msg)
    {
        // Write to logging system.
        echo "Mail ERROR: $msg\n";
    }

    function Send()
    {
        $ret_send = parent::Send();
        if (defined('MAIL_DEBUG') && MAIL_DEBUG) {
            $tmp_path = XIMDEX_ROOT_PATH .  App::getValue('TempRoot');
            $filename = tempnam($tmp_path, 'mail_');
            $data = parent::CreateBody();
            
            // Just a log, no sense to log the fsutils error too
            FsUtils::file_put_contents($filename, $data);
        }
        return $ret_send;
    }
}