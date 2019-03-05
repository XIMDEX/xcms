<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Properties;

use Ximdex\Logger;
use Ximdex\Utils\Factory;
use Ximdex\Models\Node;
use Ximdex\NodeTypes\NodeTypeConstants;

/**
 * Manager class for Inherited properties
 */
class InheritedPropertiesManager
{
	/**
	 * List of properties to manage by default
	 * 
	 * @var array
	 */
    const defaultProperties = [InheritableProperty::CHANNEL, InheritableProperty::LANGUAGE];

	/**
	 * Returns the property values
	 * 
	 * @param int $nodeId
	 * @param bool $onlyInherited
	 * @return array
	 */
	public static function getValues(int $nodeId, bool $onlyInherited = false, array $properties = null) : array
	{
		$factory = new Factory(dirname(__FILE__), '');
		$ret = array();
		if (! $properties) {
		    $properties = self::getProperties($nodeId);
		}
		foreach ($properties as $prop) {
		    $propManager = $factory->instantiate($prop . 'Property', [$nodeId], '\Ximdex\Properties');
		    if (! is_object($propManager)) {
				Logger::error('Can not instantiate inheritable property: ' . $prop);
				continue;
			}
			$ret[$prop] = $propManager->getValues($onlyInherited);
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
	 */
	public static function setValues(int $nodeId, array $properties)
	{
		$factory = new Factory(dirname(__FILE__), '');
		if (! is_array($properties)) {
		    $properties = array();
		}
		$ret = array();
		foreach (self::getProperties($nodeId) as $prop) {
		    if (! in_array($prop, array_keys($properties))) {
		        $properties[$prop] = array();
		    }
			$value = $properties[$prop];
			$propManager = $factory->instantiate($prop . 'Property', [$nodeId], 'Ximdex\Properties');
			if (! is_object($propManager)) {
				Logger::error('Can not instantiate inheritable property: ' . $prop);
				continue;
			}
			$ret[$prop] = $propManager->setValues($value);
		}
		return $ret;
	}

	/**
	 * Returns the affected nodes when deleting a property value
	 * 
	 * @param int $nodeId
	 * @param array $properties
	 * @return array
	 */
	public static function getAffectedNodes(int $nodeId, array $properties)
	{
	    return [];
	}
	
	/**
	 * Applies a property value recursively
	 * 
	 * @param string $property
	 * @param int $nodeId
	 * @param array $values
	 * @return array
	 */
	public static function applyPropertyRecursively(string $property, int $nodeId, array $values)
	{
	    $factory = new Factory(dirname(__FILE__), '');
	    $ret = array();
	    $propertyClass = $factory->instantiate($property . 'Property', [$nodeId], '\Ximdex\Properties');
	    if (! is_object($propertyClass)) {     
            Logger::error('Inheritable property cannot be instantiate: ' . $property);
	        return $ret;
	    }
	    $ret[$property] = $propertyClass->applyPropertyRecursively($values);
	    return $ret;
	}
	
	/**
	 * Return a list of property names applicable to the given node (by its node type), or default instead
	 * 
	 * @param int $nodeId
	 * @throws \Exception
	 * @return array
	 */
	private static function getProperties(int $nodeId = null) : array
	{
	    if ($nodeId) {
	        $node = new Node($nodeId);
	        if (! $node->getId()) {
	            throw new \Exception('Node with ID: ' . $nodeId . ' does not exists');
	        }
    	    if (in_array($node->getNodeType(), [NodeTypeConstants::COMMON_ROOT_FOLDER, NodeTypeConstants::COMMON_FOLDER])) {
    	        
    	        // Common folders only show channels properties
    	        return [InheritableProperty::CHANNEL];
    	    }
    	    if ($node->nodeType->isSection()) {
    	        
    	        // Sections node include matadata scheme properties
    	        return array_merge(self::defaultProperties, [InheritableProperty::METADATA_SCHEME]);
    	    }
	    }
	    return self::defaultProperties;
	}
}
