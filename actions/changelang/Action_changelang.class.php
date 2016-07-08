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

use Ximdex\Models\User;
use Ximdex\Models\XimLocale;
use Ximdex\MVC\ActionAbstract;

class Action_changelang extends ActionAbstract {
   // Main mathod: it shows the init form
  function index() {
		$locale 					= new XimLocale();
		$code 						= $this->request->getParam('code');
		$locale_selected 	= $locale->GetLocaleByCode($code);
		$error 						= true;

		if (!empty($locale_selected)){
			$user = new User(\Ximdex\Utils\Session::get('userID'));

			if ( $user->SetLocale($locale_selected["Code"]) ){
				$this->messages->add(
					sprintf(
					_("Ximdex Language changed to %s. The changes will take effect once you restart Ximdex."), _($locale_selected["Name"]) ),
					MSG_TYPE_NOTICE
				);

				$error = false;
			}
		}

		if($error) {
			$user->messages->add(_('User could not be found'), MSG_TYPE_ERROR);
		}

		$values = array('messages' => $this->messages->messages);
		$this->render($values);
  }
}
?>
