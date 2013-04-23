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



require_once (XIMDEX_ROOT_PATH . '/inc/widgets/Widget_Abstract.class.php');

class Widget_contextmenu extends Widget_Abstract {

	public function process($params) {

		//Is a predefined menu ?
		if(array_key_exists("initialize", $params) ) {
			$method = $params["initialize"];
			//method for predefined menu 
			if(method_exists($this, $method) ) {
				$this->$method($params);
			}else {
				//General template
				$this->setTemplate($params["initialize"] );
			}
		}

		$this->addCss("jquery.contextMenu.css");	

		return parent::process($params);
	}

	//Code for main predefined menu
	public function mainmenu(& $params) {
		$this->setTemplate($params["initialize"] );

		//Modify your user
		$params["userid"] = XSession::get('userID');
	
		//Change your language
		$locale = new XimLocale();
		$params["user_locale"] = $locale->GetLocaleByCode(XSession::get('locale'));
		$params["locales"]  = $locale->GetEnabledLocales();
	}
}

?>
