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

ModulesManager::file('/inc/mail/Mail.class.php');
class EmailNotificationStrategy{

	/**
	 * Send a notification.
	 * @param  string $subject   
	 * @param  string $content   
	 * @param  string $from      
	 * @param  array $to id       
	 * @param  array $extraData More required data in the message. 
	 * Maybe a attachment or whatever
	 * @return [type]            [description]
	 */
	public function sendNotification($subject, $content,$from, $to, $extraData){

		$result = array();
		foreach($to as $toUser) {
			
			$user = new User($toUser);
			$userEmail = $user->get('Email');
			$userName = $user->get('Name');	
			$mail = new Mail();
			$mail->addAddress($userEmail, $userName);
			$mail->Subject = $subject;
			$mail->Body = $content;
			if ($mail->Send()) {
				$result[$toUser] = true;				
			} else {
				$result[$toUser] = false;				
			}
		}

		return $result;
	}
}

?>