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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

use Ximdex\MVC\ActionAbstract;
use Ximdex\Models\Node;
use Ximdex\Properties\InheritedPropertiesManager;
use Ximdex\Runtime\App;
use Ximdex\NodeTypes\NodeTypeConstants;
use Ximdex\NodeTypes\NodeTypeGroupConstants;
use Ximdex\Models\NodeProperty;

/**
 * Manage properties action.
 *
 * Set the channels and language availables for the descendant nodes.
 */
class Action_manageproperties extends ActionAbstract
{
    /**
     * Main function
     * Load the manage properties form
     *
     * Request params:
     * * nodeid
     *
     * @uses InheritedProperties::getValues Get array with inherited properties from ancestor nodes
     */
    public function index()
    {
        // Load css and js resources for action form
        $this->addCss('/actions/manageproperties/resources/css/styles.css');
        $this->addJs('/actions/manageproperties/resources/js/index.js');
        $this->addJs('/actions/manageproperties/resources/js/confirm.js');
        $this->addJs('/actions/manageproperties/resources/js/dialog.js');
        $nodeId = $this->request->getParam('nodeid');
        $nodeId = $nodeId < 10000 ? 10000 : $nodeId;
        $node = new Node($nodeId);
        $properties = InheritedPropertiesManager::getValues($nodeId);
        $inProject = in_array( $node->GetNodeType(),NodeTypeGroupConstants::NODE_PROJECTS );
        $values = array(
            'properties' => $properties,
            'go_method' => 'save_changes',
            'name' => $node->GetNodeName(),
            'nodeTypeID' => $node->nodeType->getID(),
            'node_Type' => $node->nodeType->GetName(),
            'inProject' => $inProject
        );
        
        // Update the checked properties in the array (inherit or overwrite option)
        foreach ($properties as $name => $prop) {
            if ($inProject) {
                $checked = true;
            } else {
                $checked = false;
                if (! empty($prop)) {
                    foreach ($prop as $value) {
                        if ($value['Checked'] == 1) {
                            $checked = true;
                            break;
                        }
                    }
                }
            }
            if ($checked) {
                $values[sprintf('%s_inherited', $name)] = 'overwrite';
            } else {
                $values[sprintf('%s_inherited', $name)] = 'inherited';
            }
        }
        
        // Default server language for server node in prefix mode
        if ($node->GetNodeType() == NodeTypeConstants::SERVER and App::getValue('PublishPathFormat') == App::PREFIX) {
            $nodeProperty = new NodeProperty();
            $property = $nodeProperty->getProperty($node->GetID(), NodeProperty::DEFAULTSERVERLANGUAGE);
            if ($property) {
                $values[NodeProperty::DEFAULTSERVERLANGUAGE] = $property[0];
            } else {
                $values[NodeProperty::DEFAULTSERVERLANGUAGE] = 0;
            }
        }
        $this->render($values, '', 'default-3.0.tpl');
    }

    /**
     * Save the results from the form
     * 
     * @uses InheritedPropertiesManager::setValues
     *
     * Request params:
     * * nodeid
     * * confirmed
     * * inherited_channels
     * * Channel_recursive
     * * Channel
     * * inherited_languages
     * * Language
     * * inherited_schemas
     * * Schema
     * * Transformer
     */
    public function save_changes()
    {
        // Get the form properties
        $nodeId = $this->request->getParam('nodeid');
        $nodeId = $nodeId < 10000 ? 10000 : $nodeId;
        $confirmed = $this->request->getParam('confirmed');
        $confirmed = $confirmed == 'YES' ? true : false;
        
        // Channels
        $inherited_channels = $this->request->getParam('inherited_channels');
        $channel_recursive = $this->request->getParam('Channel_recursive') ? true : false;
        $channels = $this->request->getParam('Channel');
        $channels = empty($channels) || $inherited_channels == 'inherited' ? array() : $channels;
        
        // Languages
        $inherited_languages = $this->request->getParam('inherited_languages');
        $languages = $this->request->getParam('Language');
        $languages = empty($languages) || $inherited_languages == 'inherited' ? array() : $languages;
        $language_recursive = $this->request->getParam('Language_recursive') ? true : false;
        
        // Schemas
        $inherited_schemas = $this->request->getParam('inherited_schemas');
        $schemas = $this->request->getParam('Schema');
        $schemas = empty($schemas) || $inherited_schemas == 'inherited' ? array() : $schemas;
        
        // Transformer
        $transformer = $this->request->getParam('Transformer');
        $transformer = empty($transformer) ? array() : $transformer;
        
        // Metadata schemes
        $inherited_metadata = $this->request->getParam('inherited_metadata');
        $metadata_recursive = $this->request->getParam('metadata_recursive') ? true : false;
        $metadata = $this->request->getParam('metadata');
        $metadata = empty($metadata) || $inherited_metadata == 'inherited' ? array() : $metadata;
        
        // Properties information
        $properties = array(
            'Channel' => $channels,
            'Language' => $languages,
            'Schema' => $schemas,
            'Transformer' => $transformer,
            'Metadata' => $metadata
        );
        $confirm = false;
        
        // This part don't show the confirm step (wait for JSON response)
        if ($confirm) {
            $this->showConfirmation($nodeId, $properties);
        } else {
            InheritedPropertiesManager::setValues($nodeId, $properties);
            $applyResults = array();
            if ($channel_recursive) {
                $applyResults = array_merge($applyResults, $this->_applyPropertyRecursively('Channel', $nodeId, $channels));
            }
            if ($language_recursive) {
                $applyResults = array_merge($applyResults, $this->_applyPropertyRecursively('Language', $nodeId, $languages));
            }
            if ($metadata_recursive) {
                $applyResults = array_merge($applyResults, $this->_applyPropertyRecursively('Metadata', $nodeId, $metadata));
            }
            $node = new Node($nodeId);
            if ($node->GetNodeType() == NodeTypeConstants::SERVER and App::getValue('PublishPathFormat') == App::PREFIX) {
                
                // Save the value of the default server language property
                $defaultServerLanguage = (int) $this->request->getParam('default_server_language');
                if (! $defaultServerLanguage) {
                    
                    // If there is not checked any language, the first one from the project will be the selected
                    $properties = InheritedPropertiesManager::getValues($nodeId);
                    foreach ($properties['Language'] as $language) {
                        if (isset($language['Inherited']) and $language['Inherited']) {
                            $defaultServerLanguage = $language['Id'];
                            break;
                        }
                    }
                }
                if ($defaultServerLanguage) {
                    $nodeProperty = new NodeProperty();
                    $property = $nodeProperty->find(ALL, 'IdNode = ' . $nodeId . ' and property = \'' . NodeProperty::DEFAULTSERVERLANGUAGE 
                        . '\'');
                    if ($property) {
                        
                        // Update the property value
                        $nodeProperty->set('IdNode', $nodeId);
                        $nodeProperty->set('Property', NodeProperty::DEFAULTSERVERLANGUAGE);
                        $nodeProperty->set('IdNodeProperty', $property[0]['IdNodeProperty']);
                        $nodeProperty->set('Value', $defaultServerLanguage);
                        $nodeProperty->update();
                    }
                    else {
                        
                        // Create the property
                        $nodeProperty->create($nodeId, NodeProperty::DEFAULTSERVERLANGUAGE, $defaultServerLanguage);
                    }
                    $this->messages->add('Default server language has been saved', MSG_TYPE_NOTICE);
                }
            }
            $this->messages->add('Properties have been apply successfully', MSG_TYPE_NOTICE);
            $values = array(
                'messages' => $this->messages->messages,
                'goback' => true,
                'history_value' => $confirmed ? 2 : 1
            );
            $this->sendJSON($values);
        }
    }

    private function showConfirmation(int $nodeId, array $properties, array $affected = [])
    {
        $this->addJs('/actions/manageproperties/resources/js/dialog.js');
        $this->addJs('/actions/manageproperties/resources/js/confirm.js');
        foreach ($affected as $prop => $value) {
            if ($value !== false) {
                $totalNodes = count($value['nodes']);
                $totalProps = count($value['props']);
                $message = '';
                switch ($prop) {
                    case 'Channel':
                        $message = sprintf(_('A total of %s channels are going to be disassociated from %s nodes.'), $totalProps, $totalNodes);
                        break;
                    case 'Language':
                        $message = sprintf(_('A total of %s language versions are going to be deleted.'), $totalNodes);
                        break;
                }
                $this->messages->add(_($message), MSG_TYPE_WARNING);
            }
        }
        $values = array(
            'nodeId' => $nodeId,
            'properties' => $properties,
            'messages' => $this->messages->messages
        );
        $this->render($values, 'confirm', 'default-3.0.tpl');
    }

    private function showResult(int $nodeId, array $results, array $applyResults, bool $confirmed)
    {
        foreach ($results as $prop => $value) {
            if ($value !== false) {
                $affectedNodes = $value['affectedNodes'];
                if ($affectedNodes !== false) $affectedNodes = $value['affectedNodes']['affectedNodes'];
                $totalProps = count($value['values']);
                $message = array();
                if ($totalProps > 0) {
                    switch ($prop) {
                        case 'Channel':
                            if ($affectedNodes !== false) {
                                $totalNodes = is_array($affectedNodes['nodes']) ? count($affectedNodes['nodes']) : 0;
                                $totalProps = is_array($affectedNodes['props']) ? count($affectedNodes['props']) : 0;
                                $message[] = sprintf(_('A total of %s channels have been disassociated from %s nodes.')
                                    , $totalProps, $totalNodes);
                            } else {
                                if ($totalProps == 0) {
                                    $message[] = _('Channel values will be inherited.');
                                } else {
                                    $message[] = sprintf(_('%s Channels have been successfully assigned.'), $totalProps);
                                }
                            }
                            if (isset($applyResults['Channel']) && $applyResults['Channel'] !== false 
                                && $applyResults['Channel']['nodes'] > 0) {
                                $message[] = sprintf(
                                    _('A total of %s channels have been recursively associated with %s documents.'),
                                    count($applyResults['Channel']['values']),
                                    $applyResults['Channel']['nodes']
                                );
                            }
                            break;
                        case 'Language':
                            if ($affectedNodes !== false) {
                                $totalProps = is_array($affectedNodes['props']) ? count($affectedNodes['props']) : 0;
                                $message[] = sprintf(_('A total of %s language versions have been deleted.'), $totalProps);
                            } else {
                                if ($totalProps == 0) {
                                    $message[] = _('Language values will be inherited.');
                                } else {
                                    $message[] = sprintf(_('%s Languages have been successfully assigned.'), $totalProps);
                                }
                            }
                            if (isset($applyResults['Language']) && $applyResults['Language'] !== false 
                                && $applyResults['Language']['nodes'] > 0) {
                                $message[] = sprintf(
                                    _('A total of %s language versions have been recursively created.'),
                                    count($applyResults['Language']['values'])
                                );
                            }
                            break;
                        case 'Schema':
                            if ($totalProps == 0) {
                                $message[] = _('Template values will be inherited.');
                            } else {
                                $message[] = sprintf(_('%s Templates have been successfully assigned.'), $totalProps);
                            }
                            break;
                        case 'Transformer':
                            if (empty($value['values'])) $value['values'] = null;
                            $transformer = (is_array($value['values'])) ? $value['values'][0] : $value['values'];
                            $message[] = sprintf(_('%s will be used as document transformer.'), $transformer);
                            break;
                    }
                }
                foreach ($message as $msg) {
                    $this->messages->add($msg, MSG_TYPE_NOTICE);
                }
            }
        }
        $values = array(
            'messages' => $this->messages->messages,
            'goback' => true,
            'history_value' => $confirmed ? 2 : 1
        );
        $this->sendJSON($values);
    }
    
    public function applyPropertyRecursively()
    {   
        $nodeId = (int) $this->request->getParam('nodeid');
        $nodeId = $nodeId < 10000 ? 10000 : $nodeId;
        $property = $this->request->getParam('property');
        $values = (array) $this->request->getParam('values');
        $result = $this->_applyPropertyRecursively($property, $nodeId, $values);
        $this->sendJSON(array('nodeId' => $nodeId, 'property' => $property, 'result' => $result[$property]));
    }
    
    private function _applyPropertyRecursively(string $property, int $nodeId, array $values)
    {
        $result = InheritedPropertiesManager::applyPropertyRecursively($property, $nodeId, $values);
        return $result;
    }
}
