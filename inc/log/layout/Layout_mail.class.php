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
 *  @version $Revision: 7740 $
 */



/**
 *
 */
class Layout_Mail extends Layout {

	function Layout_Mail($template) {
		parent::Layout($template);
	}

	function & format(&$event) {

		$string = $this->_template;
		switch ($event->getParam("priority")) {
			case LOGGER_LEVEL_DEBUG:
				$severity = 'DEBUG';
				break;
			case LOGGER_LEVEL_INFO:
				$severity = 'INFO';
				break;
			case LOGGER_LEVEL_WARNING:
				$severity = 'WARNING';
				break;
			case LOGGER_LEVEL_ERROR:
				$severity = 'ERROR';
				break;
			case LOGGER_LEVEL_FATAL:
				$severity = 'FATAL';
				break;
		}
		
		$string = str_replace("%fn",   	$event->getParam("function"),   $string);
		$string = str_replace("%c",    	$event->getParam("class"),      $string);
		$string = str_replace("%f",    	$event->getParam("file"),       $string);
		$string = str_replace("%l",		$event->getParam("line"),       $string);
		$string = str_replace("%m",    	$event->getParam("message"),    $string);
		$string = str_replace("%p",    	$severity,   $string);
		$string = str_replace("%d",    	$event->getParam("date"),       $string);
		$string = str_replace("%t",    	$event->getParam("time"),       $string);

		return $string;
	}

}
?>