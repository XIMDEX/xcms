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

use Ximdex\MVC\ActionAbstract;
use Ximdex\Widgets\Widget;

class Action_widgets extends ActionAbstract {

  function index() {
		$widget = strtolower($this->request->getParam("widget"));
		$wmethod = ($this->request->getParam("wmethod"))? $this->request->getParam("wmethod") : "update";
		$wpath = XIMDEX_ROOT_PATH."/inc/widgets/";

		$class = "Widget_{$widget}";

		if(file_exists("{$wpath}{$class}.class.php") ) {
			require_once("{$wpath}{$class}.class.php");

			if(method_exists($class, $wmethod) ) {
				$class::$wmethod($this->request->getRequests());
			}else {
				$class::update($this->request->getRequests());
			}

		}

		die();
	}

	function get_widget() {
		$widget = strtolower($this->request->getParam("widget"));
		$params = $this->request->getRequests();


		echo  Widget::create($widget, $params);

		die();
	}

}