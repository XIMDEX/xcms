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



ModulesManager::file('/inc/patterns/Factory.class.php');

/**
 * Manager class for Inherited properties
 */
class InheritedPropertiesManager {

	/**
	 * list of properties to manage
	 * @var array
	 */
	static protected $properties = array('Channel', 'Language');

	/**
	 * Returns the property values
	 * 
	 * @param integer $nodeId
	 *
	 * @return array Associative array $ret["property_name"] = array_values
	 *
	 * @uses ChannelProperty::getValues to get the specific values.
	 * @uses LanguageProperty::getValues to get the specific values. 
	 * 
	 */
	static public function getValues($nodeId) {

		$factory = new Factory(dirname(__FILE__), '');
		$ret = array();

		foreach (self::$properties as $prop) {

			$p = $factory->instantiate($prop . 'Property', $nodeId);

			if (!is_object($p)) {
				XMD_Log::error(_("Inheritable property cannot be instantiate: ") . $prop);
				continue;
			}

			$ret[$prop] = $p->getValues();
		}

		return $ret;
	}

	/**
	 * Sets the property values
	 * 
	 * @param integer $nodeId
	 * @param mixed $properties The values
	 *
	 * @uses ChannelProperty::setValues to set the specific values.
	 * @uses LanguageProperty::setValues to set the specific values. 
	 *
	 * @return array Associative array $ret["property_name"] = array_values
	 * 
	 */
	static public function setValues($nodeId, $properties) {

		$factory = new Factory(dirname(__FILE__), '');
		if (!is_array($properties)) $properties = array();
		$ret = array();

		foreach (self::$properties as $prop) {

			if (!in_array($prop, array_keys($properties))) $properties[$prop] = array();
			$value = $properties[$prop];

			$p = $factory->instantiate($prop . 'Property', $nodeId);
			if (!is_object($p)) {
				XMD_Log::error("Inheritable property cannot be instantiate: " . $prop);
				continue;
			}

			$ret[$prop] = $p->setValues($value);
		}

		return $ret;
	}

	/**
	 * Returns the affected nodes when deleting a property value
	 * 
	 * @param integer $nodeId
	 * @param mixed $properties Values to be deleted
	 *
	 * @uses ChannelProperty::getAffectedNodes 
	 * @uses LanguageProperty::getAffectedNodes
	 * 
	 */
	static public function getAffectedNodes($nodeId, $properties) {

		$factory = new Factory(dirname(__FILE__), '');
		if (!is_array($properties)) $properties = array();
		$ret = array();

		foreach (self::$properties as $prop) {

			if (!in_array($prop, array_keys($properties))) $properties[$prop] = array();
			$value = $properties[$prop];

			$p = $factory->instantiate($prop . 'Property', $nodeId);
			if (!is_object($p)) {
				XMD_Log::error(_("Inheritable property cannot be instantiate: ") . $prop);
				continue;
			}

			$ret[$prop] = $p->getAffectedNodes($value);
		}

		return $ret;
	}

	/**
	 * Applies a property value recursively
	 * @param string $property
	 * @param integer $nodeId
	 * @param mixed $values
	 */
	static public function applyPropertyRecursively($property, $nodeId, $values) {

		$factory = new Factory(dirname(__FILE__), '');
		$ret = array();

		$p = $factory->instantiate($property . 'Property', $nodeId);
		if (!is_object($p)) {
			XMD_Log::error(_("Inheritable property cannot be instantiate: ") . $property);
			return $ret;
		}

		$ret[$property] = $p->applyPropertyRecursively($values);

		return $ret;
	}

}
