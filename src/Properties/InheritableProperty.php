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
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Models\FastTraverse;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\Models\NodeProperty;
use Ximdex\NodeTypes\NodeTypeGroupConstants;

/**
 * Abstract inheritable property class
 */
abstract class InheritableProperty
{
    // Contanst for properties names
    const CHANNEL = 'Channel';
    const LANGUAGE = 'Language';
    const METADATA_SCHEME = 'Metadata';
    
    /**
     * nodeid for current node
     * 
     * @var integer
     */
    protected $nodeId = null;

    /**
     * Node object
     * 
     * @var Object
     */
    protected $node = null;

    /**
     * NodeTypeId for current node
     * 
     * @var integer
     */
    protected $nodeTypeId = null;

    /**
     * NodeType object
     * 
     * @var Object
     */
    protected $nodeType = null;

    /**
     * Construct method
     * Load the properties of the class
     *
     * @param int $nodeid : Node identificator
     */
    public function __construct(int $nodeId)
    {
        $this->nodeId = $nodeId;
        $this->node = new Node($nodeId);
        if ($this->node->get('IdNode') < 1) {
            Logger::error('There is no value for node ID when InheritableProperty was instanced');
        }
        $this->nodeTypeId = $this->node->get('IdNodeType');
        $this->nodeType = new NodeType($this->nodeTypeId);
    }

    /**
     * Returns the property name that class represents
     */
    abstract public function getPropertyName();

    /**
     * Returns the property values
     * 
     * @param bool $onlyInherited
     * @return boolean|array
     */
    public function getValues(bool $onlyInherited = false)
    {
        // Selected channels on the node
        $nodeProperties = $this->getProperty(false);
        if (empty($nodeProperties)) {
            $nodeProperties = array();
        }
        if ( !in_array( $this->nodeTypeId,NodeTypeGroupConstants::NODE_PROJECTS ) ) {
            
            // All the project properties will be the available ones
            $projectNode = new Node($this->node->getProject());
            if (! $projectNode->GetID()) {
                Logger::error('Cannot load the project with node ID: ' . $this->node->getProject());
                return false;
            }
            $availableProperties = $projectNode->getProperty($this->getPropertyName(), false);
            if (! $availableProperties) {
                
                // If the project has no specified properties, then the system will be the availables
                $availableProperties = $this->get_system_properties();
            } else {
                
                // Obtain the information about each property ID
                $availableProperties = $this->get_inherit_properties($availableProperties);
            }
            
            // Nodes below the Project shows only inherited channels
            $parentId = $this->node->getParent();
            $parent = new Node($parentId);
            $inheritProperties = $parent->getProperty($this->getPropertyName());
        } else {
            
            // The Project node shows all the system channels as availables
            $availableProperties = $this->get_system_properties();
        }
        
        // For each available property, assing the the values in use
        $values = [];
        foreach ($availableProperties as & $property) {
            
            // If is availableChannel and nodeChannels is empty, we use the availableChannels
            if ($nodeProperties) {
                $property['Checked'] = in_array($property['Id'], $nodeProperties) ? true : false;
                if ($onlyInherited and ! $property['Checked']) {
                    continue;
                }
            } else {
                $property['Checked'] = false;
            }
            
            // Update the inherit value in the result
            if (isset($inheritProperties)) {
                $property['Inherited'] = in_array($property['Id'], $inheritProperties) ? true : false;
            } else {
                $property['Inherited'] = true;
            }
            if ($onlyInherited and !$property['Inherited'] and !$property['Checked']) {
                continue;
            }
            
            // Save the result with the property ID with index
            unset($property[0]);
            unset($property[1]);
            $values[$property['Id']] = $property;
        }
        
        // Returning available properties with the activated ones for the current node
        return $values;
    }

    /**
     * Sets the property values
     * 
     * @param array $values
     * @return array
     */
    public function setValues(array $values)
    {
        if (! is_array($values)) {
            $values = array();
        }
        $affectedNodes = $this->updateAffectedNodes($values);
        $this->deleteProperty($values);
        if (is_array($values) && count($values) > 0) {
            $this->setProperty($values);
        }
        return array(
            'affectedNodes' => $affectedNodes,
            'values' => $values
        );
    }

    /**
     * Returns the affected nodes when deleting a property value
     * 
     * @param array $values
     * @return boolean
     */
    protected function getAffectedNodes(array $values)
    {
        return [];
    }

    /**
     * Returns the affected properties
     * 
     * @param array $values
     * @return array
     */
    protected function getAffectedProperties(array $values)
    {
        if (! is_array($values) || count($values) == 0) {
            return array();
        }
        
        // Selected properties on node
        $nodeProperties = $this->getProperty(false);
        if (empty($nodeProperties)) {
            $nodeProperties = array();
        }
        
        // Properties to be deleted
        $propertiesToDelete = array_diff($nodeProperties, $values);
        return $propertiesToDelete;
    }

    /**
     * Returns the property values
     * 
     * @param bool $inherited
     * @return boolean|NULL|array
     */
    protected function getProperty(bool $inherited = true)
    {
        $prop = $this->getPropertyName();
        return $this->node->getProperty($prop, $inherited);
    }

    /**
     * Sets the property values
     * 
     * @param array $values
     */
    protected function setProperty(array $values)
    {
        $prop = $this->getPropertyName();
        return $this->node->setProperty($prop, $values);
    }

    /**
     * Deletes the property values
     * 
     * @param array $values
     * @return boolean
     */
    protected function deleteProperty(array $values)
    {
        $this->deleteChildrenProperties($values);
        $prop = $this->getPropertyName();
        $ret = $this->node->deleteProperty($prop);
        return $ret;
    }

    /**
     * Delete the properties of children nodes
     * 
     * @param array $values
     */
    protected function deleteChildrenProperties(array $values)
    {
        $propertiesToDelete = $this->getAffectedProperties($values);
        if (count($propertiesToDelete) == 0) {
            return;
        }
        $prop = $this->getPropertyName();
        $db = new \Ximdex\Runtime\Db();
        $sql = "select distinct(p.IdNode) as IdNode
				from FastTraverse f  join NodeProperties p on f.idchild = p.idnode
				where f.idnode = %s
					and f.depth > 0
					and p.property = '%s'
					and p.value in ('%s')";
        $sql = sprintf($sql, $this->nodeId, $prop, implode("', '", $propertiesToDelete));
        $db->query($sql);
        while (! $db->EOF) {
            $childId = $db->getValue('IdNode');
            $child = new Node($childId);
            foreach ($propertiesToDelete as $value) {
                $child->deletePropertyValue($prop, $value);
            }
            $db->next();
        }
    }

    /**
     * Obtain updated nodes or false is there is not changes
     * 
     * @param array $values
     */
    abstract protected function updateAffectedNodes(array $values);

    /**
     * Obtain the system properties
     */
    abstract protected function get_system_properties();

    /**
     * Obtain the local or inherited properties from available an array of properties ID
     */
    abstract protected function get_inherit_properties(array $availableProperties);

    /**
     * Applies the property values recursively deleting all specified properties in its children
     * 
     * @param array $values
     */
    public function applyPropertyRecursively(array $values = [])
    {
        if (empty($values)) {
            return false;
        }
        
        // Obtain the target nodes under the specified one
        $children = FastTraverse::getChildren($this->nodeId, [
            'node' => [
                'IdNodeType'
            ]
        ], null, array(
            'include' => array(
                'IdNodeType' => array(
                    NodeTypeConstants::SERVER,
                    NodeTypeConstants::SECTION,
                    NodeTypeConstants::XML_ROOT_FOLDER,
                    NodeTypeConstants::XML_CONTAINER,
                    NodeTypeConstants::HTML_CONTAINER
                )
            )
        ));
        if ($children === false) {
            return false;
        }
        if (isset($children[0])) {
            unset($children[0]);
        }
        
        // For each node, delete the local property if that one exists
        $nodes = 0;
        $nodeProperty = new NodeProperty();
        foreach ($children as $level) {
            foreach ($level as $child => $type) {
                if ($nodeProperty->getProperty($child, $this->getPropertyName())) {
                    if ($nodeProperty->deleteByNodeProperty($child, $this->getPropertyName()) === false) {
                        Logger::error('Cannot apply recursively deleting the property ' . $this->getPropertyName() 
                            . ' in the node ' . $child . ' with node type ' . $type);
                        return false;
                    }
                    $nodes ++;
                }
            }
        }
        return array(
            'nodes' => $nodes,
            'values' => $values
        );
    }
}
