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



include_once(XIMDEX_ROOT_PATH . '/inc/mail/Mail.class.php');
include_once(XIMDEX_ROOT_PATH . '/inc/model/node.inc');
require_once(XIMDEX_ROOT_PATH . "/inc/persistence/XSession.class.php");

/**
 *
 */
class Appender_mail extends Appender {

	var $_mail;
	var $_mailboxes;

	/**
	 * @param object params['layout']
	 */
	function Appender_mail($params) {

		$this->setLayout($params['layout']);
		$this->_mail = new Mail();

		$this->_mailboxes = is_array($params['mailboxes']) ? $params['mailboxes'] : array($params['mailboxes']);

		$user = new User(301);
		$defaultMail = $user->Get('Email');
		$this->_mailboxes[] = $defaultMail;
		$this->_mailboxes = array_unique($this->_mailboxes);
	}

	function open($file=null) {
		return true;
	}

	function write(&$event) {

		// Automatically call layout and transform. (Transformated msg in $this->_msg)
		parent::write($event);

		$this->_mail->clearAddresses();
		foreach ($this->_mailboxes as $mailbox) {
			$this->_mail->addAddress($mailbox);
		}

		$ximid= Config::getValue('ximid');

		$this->_mail->Subject = "[$ximid] Notificaciones de Ximdex";
		$this->_mail->Body = $this->_msg;
		$this->_mail->Send();
	}

	function close() {
	}
}

?>