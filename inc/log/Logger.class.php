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


if (file_exists(XIMDEX_ROOT_PATH . "/conf/install-params.conf.php"))
	include_once(XIMDEX_ROOT_PATH . "/conf/install-params.conf.php");

if (!defined('LOGGER_LEVEL_ALL'))		define('LOGGER_LEVEL_ALL',		0x0000);
if (!defined('LOGGER_LEVEL_DEBUG'))		define('LOGGER_LEVEL_DEBUG',	0x0001);
if (!defined('LOGGER_LEVEL_INFO'))		define('LOGGER_LEVEL_INFO',		0x0002);
if (!defined('LOGGER_LEVEL_WARNING'))	define('LOGGER_LEVEL_WARNING',	0x0003);
if (!defined('LOGGER_LEVEL_ERROR'))		define('LOGGER_LEVEL_ERROR',	0x0004);
if (!defined('LOGGER_LEVEL_FATAL'))		define('LOGGER_LEVEL_FATAL',	0x0005);
if (!defined('LOGGER_LEVEL_NONE'))		define('LOGGER_LEVEL_NONE',		0xFFFF);

/**
 *
 */
class Logger {

	/**
	 *
	 * @var unknown_type
	 */
	var $_name;
	/**
	 *
	 * @var unknown_type
	 */
	var $_params;
	/**
	 * split parameters in params in unique attributes.
	 * @var unknown_type
	 */
	var $_priority;
	/**
	 * Array of appenders.
	 *
	 * @var array
	 */
	var $_appenders;

	/**
	 * Constructor
	 * @param $name
	 * @param $params
	 * @return unknown_type
	 */
	function Logger($name, $params) {

		// Init data structure.
		$this->_appenders = new \Ximdex\Utils\AssociativeArray();
		$this->_getters = new \Ximdex\Utils\AssociativeArray();

		$this->_name = $name;
		$this->_params = $params;

		// TODO: check params!
		$this->_priority = $params['priority'];
	}

	/**
	 *
	 * @param $name
	 * @param $appender
	 * @return unknown_type
	 */
	function attachAppender($name, &$appender) {

		$this->_appenders->set($name, $appender);
	}

	/**
	 *
	 * @param $name
	 * @param $getter
	 * @return unknown_type
	 */
	function attachGetter($name, &$getter) {

		$this->_getters->set($name, $getter);
	}

	/**
	 *
	 * @param $name
	 * @return unknown_type
	 */
	function detachAppender($name) {

		$this->_appenders->del($name);
	}

	/**
	 *
	 * @return unknown_type
	 */
	function & getAppenders() {

		return $this->_appenders->getArray();
	}

	/**
	 *
	 * @return unknown_type
	 */
	function & getGetters() {

		return $this->_getters->getArray();
	}

	/**
	 *
	 * @param $name
	 * @return unknown_type
	 */
	function & detachGetter($name) {

		return $this->_getters->del($name);
	}

	/**
	 *
	 * @param $msg
	 * @param $priority
	 * @return unknown_type
	 */
	function log($msg, $priority) {

		if ($priority >= $this->_priority) {

			// capture trace info...
			// note that traceinfo[0] is the info of current script and
			// traceinfo[1] is just past script info.
			$traceinfo = debug_backtrace();

			if (is_array($this->_params) && array_key_exists('backtrace', $this->_params)) {
				$idx = $this->_params['backtrace'];
			} else {
				$idx = '3';
			}


			$event = new Event();
			$event->setParam("priority",    $priority);
			$event->setParam("message",     $msg);
			$event->setParam("class",       isset( $traceinfo[$idx]['class'] ) ? $traceinfo[$idx]['class'] : '' );
			$event->setParam("file",        isset( $traceinfo[$idx]['file'] ) ? $traceinfo[$idx]['file'] : '' );
			$event->setParam("line",        isset( $traceinfo[$idx]['line'] ) ? $traceinfo[$idx]['line'] : '' );
			$event->setParam("function",    strtoupper(isset( $traceinfo[$idx]['function'] ) ? $traceinfo[$idx]['function'] : '' ));
			$event->setParam("date",        date("Y-m-d"));
			$event->setParam("time",        date("G:i:s"));

			$listOfAppenders = $this->getAppenders();

			if ($listOfAppenders != null) {
				foreach($listOfAppenders as $appender) {
					$appender->write($event);
				}
			}
		}
	}

	/**
	 *
	 * @return unknown_type
	 */
	function read() {

		$listOfGetters = $this->getGetters();
		$responses = array();

		if ($listOfGetters != null) {
			foreach($listOfGetters as $name=>$getter) {
				$text = $getter->read();
				$parser = $getter->getParams();
				$parser = isset($parser['parser']) ? $parser['parser'] : 'SyntaxParser_Simple';

				// En caso de que se pueda annadir mas de un getter usar $responses[$name]
				$responses[0] = array();
				$responses[0]['parser'] = $parser;
				$responses[0]['text'] = $text;
			}
		}

		// El sistema realmente no contempla el hecho de annadir mas de un Getter al Logger
		return $responses[0];
	}
}

?>