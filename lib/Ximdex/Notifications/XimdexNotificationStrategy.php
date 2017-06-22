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

namespace Ximdex\Notifications;

use Messages_ORM;
use ModulesManager;

ModulesManager::file('/inc/model/orm/Messages_ORM.class.php');

class XimdexNotificationStrategy
{

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
	public function sendNotification($subject, $content, $from, $to)
	{

		$result = array();
		$nowDate = date('Y-m-d H:i:s');
		foreach ($to as $toUser) {
			$messages = new Messages_ORM();
			$messages->set("IdFrom", $from);
			$messages->set("IdOwner", $toUser);
			$messages->set("Subject", $subject);
			$messages->set("Content", $content);
			if ($messages->add()) {
				$result[$toUser] = true;
			} else {
				$result[$toUser] = false;
			}
		}

		return $result;
	}
}
