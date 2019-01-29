<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\NodeType;
use Ximdex\Runtime\App;
use Ximdex\Runtime\Constants;
use Ximdex\Utils\FsUtils;

define('COPY_MODE', true);
define('IMPORT_MODE', false);

class ImportXml
{
    /**
     * The elements of this array are going to cause that the parser will change to control mode
     * 
     * @var array
     */
    public $tagsForContainControls = array('CHANNELMANAGER', 'LANGUAGEMANAGER', 'GROUPMANAGER');
    
    /**
     * The elements of this array are going to be inserted as father sons, always that father is not in advancewritting
     *
     * @var array
     */
    public $tagsForControl = array('CHANNEL', 'LANGUAGE', 'GROUP');
    
    /**
     * Types of template
     *
     * @var array
     */
    public $templateTypes = array('VISUALTEMPLATE', 'RNGVISUALTEMPLATE', 'HTMLLAYOUT');

    /**
     * The elements of this array are going to be executed in startElement, that means that no son is going to be contained
     *
     * @var array
     */
    public $tagsForAdvancedWritting = null;
    public $relations = array();

    /**
     * The elements of this array can contain a visualtemplate, but they will not cause a insertion
     *
     * @var array
     */
    public $tagsWithVisualTemplate = null;
    public $depth = 0;
    public $rootNode = 0;
    public $importationId = 0;
    public $file = '';
    public $idfinal = 0;
    public $importationDate = '';
    public $tree = null;
    public $dbObj = null;
    public $messages = null;
    public $bindedNodes = null;
    public $pendingNodes = null;
    public $pendingRelations = null;
    
    /**
     * Associations received by the importation function
     *
     * @var array
     */
    public $nodeAssociations = null;
    public $processedNodes = null;
    public $timeStamp = null;
    public $_firstExportationNode = null;
    public $_processIsParsing = false;
    public $abort = false;
    public $firstNode = true;
    public $controlMode = false;
    public $heuristicMode = false;
    
    /**
     * Limit case in which just a pvd is going to be copied. By default, this variable is true, and otherwise the var value 
     * will change when going to startelement
     *
     * @var boolean
     */
    public $inTemplateFolder = true;
    public $_insertFirstNode = false;
    public $idXimio = 0;
    public $bufferedData = '';
    public $sugestedPackages = null;
    public $_recurrence = null;

    public $idLocalXimio = null;
    public $mode = IMPORT_MODE;

    /**
     * Class construct
     *
     * @param int $rootNode node where importation starts
     * @param string $file Base name of the importation file
     * @param array $nodeAssociations Association from old nodetype to new nodetype
     * @param bool $mode Constants:RUN_HEURISTIC_MODE || Constants:RUN_IMPORT_MODE
     * @param int $firstExportationNode Exportation node from where importation starts
     * @return ImportXml
     */
    function __construct($rootNode, $file, $nodeAssociations, $mode = Constants::RUN_HEURISTIC_MODE, $recurrence = null
        , $firstExportationNode = null, $insertFirstNode = false)
    {
        $dbObj = new Ximdex\Runtime\Db();

        // Obtaining a list of nodes for $tagsForAdvancedWritting
        // These nodes ca be inserted without inserting first its descendants. For mor clarity, these nodes are containers
        // In general, they are all nodes with the associated class 'foldernode', aalthough 'servernode' are special cases
        $query = "select IdNodeType, upper(Name) as Name
			from NodeTypes
			where class in ('foldernode', 'servernode')
			order by Name";
        $dbObj->Query($query);
        $this->tagsForAdvancedWritting = array();
        while (!$dbObj->EOF) {
            $this->tagsForAdvancedWritting[] = $dbObj->GetValue('Name');
            $dbObj->Next();
        }

        // Obtaining a list of nodes for $tagsWithVisualTemplate
        // These nodes have an associated PVD. XimIO will treat these nodes in an appropriate way to insert their PVDs.
        // The nodes which have associated the PVD are the containers, so, knowing the nodetypes,
        // which are structured documents, these containers can be obtained through the table NodeAllowedContents.
        $query = "select n.IdNodeType, upper(n.Name) as Name
			from NodeAllowedContents na, NodeTypes n
			where na.NodeType in (
				select IdNodeType from NodeTypes where IsStructuredDocument = 1
				)
			and na.IdNodeType = n.IdNodeType
			order by Name";
        $dbObj->Query($query);
        $this->tagsWithVisualTemplate = array();
        while (!$dbObj->EOF) {
            $this->tagsWithVisualTemplate[] = $dbObj->GetValue('Name');
            $dbObj->Next();
        }
        $this->sugestedPackages = array();
        $this->rootNode = $rootNode;
        if (!is_null($file)) {
            $this->file = sprintf("%s/data/backup/%s_ximio/ximio.xml", XIMDEX_ROOT_PATH, $file);
            $this->timeStamp = $file;
        } else {
            $this->timeStamp = 0;
        }
        $this->messages = array();
        $this->tree = array();
        $this->nodeAssociations = is_array($nodeAssociations) ? $nodeAssociations : array();
        $this->_checkAssociations();
        $this->bindedNodes = array();
        $this->heuristicMode = $mode;
        $this->pendingNodes = array();
        $this->_firstExportationNode = $firstExportationNode;
        $this->_insertFirstNode = (bool)$insertFirstNode;
        $this->_recurrence = $recurrence;
        $this->idLocalXimio = App::getValue('ximid');
        $this->processedNodes = array();
        $this->processedNodes['success'] = 0;
        $this->processedNodes['failed'] = array();
        $this->processedNodes['failed'][Constants::ERROR_INCORRECT_DATA] = 0;
        $this->processedNodes['failed'][Constants::ERROR_NO_PERMISSIONS] = 0;
        $this->processedNodes['failed'][Constants::ERROR_NOT_REACHED] = 0;
        $this->processedNodes['failed'][Constants::ERROR_NOT_ALLOWED] = 0;
    }

    function _checkAssociations()
    {
        if (!$this->file) {
            return;
        }
        $content = FsUtils::file_get_contents($this->file);
        foreach ($this->nodeAssociations as $oldId => $newId) {
            $node = new Node($newId);
            if (!($node->get('IdNode') > 0)) {
                $this->messages[] = sprintf(_("The association %s=%s has been dismissed due to the node %s is not existing in the destiny ximdex"), $oldId, $newId, $newId);
                unset($this->nodeAssociations[$oldId]);
                continue;
            }
            $associationOk = false;
            $matches = null;
            if (preg_match(sprintf('/(<.*id="%s".*>)/', $oldId), $content, $matches) > 0) {
                $match = isset($matches[1]) ? $matches[1] : null;
                if (preg_match('/<\s*(\w*)\s+/', $match, $matches) > 0) {
                    $nodeTypeName = isset($matches[1]) ? $matches[1] : null;
                    $nodetype = new NodeType();
                    $nodetype->SetByName($nodeTypeName);
                    if ($nodetype->get('IdNodeType') == $node->nodeType->get('IdNodeType')) {
                        $associationOk = true;
                    }
                }
            }
            if (!$associationOk) {
                $this->messages[] = sprintf(_("The association %s=%s has been dismissed due to the source node %s is not of the same type than the destiny one or type could not been estimated"), $oldId, $newId, $oldId);
            }
        }
    }

    /**
     * Function which imports the content
     *
     * @return bool (for the moment, it should return an array with more detailed information)
     */
    function copy($xml)
    {
        $xmlLines = explode("\n", $xml);
        $this->timeStamp = 0;
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this);
        xml_set_element_handler($xml_parser,
            array(& $this, '_startElement'),
            array(& $this, '_endElement'));
        xml_set_character_data_handler($xml_parser, array(& $this, '_characterDataHandler'));
        $lines = count($xmlLines);
        foreach ($xmlLines as $key => $data) {
            if ($this->abort) {
                break;
            }
            if (!xml_parse($xml_parser, $data, ($key + 1) == $lines)) {
                die(sprintf(_("XML error: %s at line %d"),
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
            }
        }
        xml_parser_free($xml_parser);
        $this->_resolvePendingNodes();
        $this->_resolvePendingRelations();
        if (is_array($this->pendingNodes)) {
            foreach ($this->pendingNodes as $pendingNode) {
                $this->processedNodes['failed'][Constants::ERROR_NOT_REACHED] += count($pendingNode);
                if (is_array($pendingNode)) {
                    foreach ($pendingNode as $node) {
                        if (isset($node['ID']) && (int)$node['ID'] > 0) {
                            $path = is_array($node['CHILDRENS']) ? $this->_getPath($node['CHILDRENS']) : '';
                            
                            // TODO parentid o oldparentid?? it should be debugged
                            $this->_bindNode($node['ID'], 0, $node['OLDPARENTID'], Constants::ERROR_NOT_REACHED, $path);
                        }
                    }
                }
            }
        }
        return $this->abort;
    }

    function _resolvePendingRelations()
    {
        $unsetArray = array('IDREL');
        $resolveArray = array('IDNEW', 'IDCOLECTOR', 'LANGID');
        if (!is_array($this->pendingRelations)) {
            return false;
        }
        foreach ($this->pendingRelations as $relation) {
            foreach ($relation as $key => $value) {
                if (in_array($key, $unsetArray)) {
                    unset($relation[$key]);
                }
                if (in_array(strtoupper($key), $resolveArray)) {
                    $db = new Ximdex\Runtime\Db();
                    $query = sprintf("SELECT IdImportationNode"
                        . " FROM XimIONodeTranslations"
                        . " WHERE IdExportationNode = %s",
                        $db->sqlEscapeString($value));
                    $db->query($query);
                    if ($db->numRows == 1) {
                        $relation[$key] = $db->getValue('IdImportationNode');
                    } else {
                        $relation[$key] = null;
                    }
                }
            }
        }
        return true;
    }

    function _resolvePendingNodes()
    {
        if ($this->mode == IMPORT_MODE) {
            return false;
        }
        
        // A copy mode should be placed here somehow
        $retry = true;
        while ($retry) {
            $retry = false;
            foreach ($this->pendingNodes as $pendingNode => $pendingNodeData) {
                foreach ($pendingNodeData as $id => $children) {
                    
                    // Foreach a copy of the var has been made, so we delete it
                    unset($this->pendingNodes[$pendingNode][$id]);
                    
                    // Trying to resolve its nodes first
                    if (!empty($children['CHILDRENS'])) {
                        foreach ($children['CHILDRENS'] as $childrenPosition => $childrenElement) {
                            $children['CHILDRENS'][$childrenPosition] = $this->_resolveNode($childrenElement, true);
                        }
                    }
                    $children = $this->_resolveNode($children, true);
                    if ($this->_insertElement($children, true)) {
                        $retry = true;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Function which imports the content
     *
     * @return bool (for the moment, it should return an array with more detailed information)
     */
    function import()
    {
        if (!($fp = fopen($this->file, "r"))) {
            return false;
        }
        $xml_parser = xml_parser_create();
        xml_set_object($xml_parser, $this);
        xml_set_element_handler($xml_parser,
            array(& $this, '_startElement'),
            array(& $this, '_endElement'));
        xml_set_character_data_handler($xml_parser, array(& $this, '_characterDataHandler'));
        while (($data = fgets($fp)) && !$this->abort) {
            if (!xml_parse($xml_parser, $data, feof($fp))) {
                die(sprintf(_("XML error: %s at line %d"),
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
            }
        }
        xml_parser_free($xml_parser);
        $this->_resolvePendingNodes();
        if (is_array($this->pendingNodes)) {
            foreach ($this->pendingNodes as $pendingNode) {
                $this->processedNodes['failed'][Constants::ERROR_NOT_REACHED] += count($pendingNode);
                if (is_array($pendingNode)) {
                    foreach ($pendingNode as $node) {
                        if (isset($node['ID']) && (int)$node['ID'] > 0) {
                            $path = is_array($node['CHILDRENS']) ? $this->_getPath($node['CHILDRENS']) : '';
                            
                            // TODO parentid o oldparentid?? it should be debugged
                            $this->_bindNode($node['ID'], 0, $node['OLDPARENTID'], Constants::ERROR_NOT_REACHED, $path);
                        }
                    }
                }
            }
        }
        $this->_resolvePendingRelations();
        return $this->abort;
    }
    
    /**
     * Function for init element parsing
     *
     * @param $parser
     * @param string $name
     * @param array $attrs
     */
    function _startElement($parser, $name, $attrs)
    {
        // Taking needed datils from header and aborting
        if ($name == Constants::HEADER) {
            $this->importationId = $this->_insertImportationInfo($attrs['ID'], $this->timeStamp);
            $this->idXimio = $attrs['ID'];
            if (!($this->importationId > 0)) {
                $this->abort = true;
                $this->messages[] = _('Exportation identifier could not been obtained');
            }
            return;
        }
        foreach ($attrs as $key => $value) {
            $attrs[$key] = utf8_decode($value);
        }

        // If some kind of tag is detected in the header, passing to control mode
        if (in_array($name, $this->tagsForContainControls)) {
            $this->controlMode = true;
            return;
        }

        // If some kind of tag is received from header
        if (in_array($name, $this->tagsForControl) && $this->controlMode) {
            
            // If in the interface it was associated or ximio source == ximio destiny, we link it, otherwise, we ignore it.
            if (isset($attrs['ID']) && (array_key_exists($attrs['ID'], $this->nodeAssociations) || ($this->idLocalXimio == $this->idXimio))) {
                $this->_bindControlElement($name, $attrs);
                unset($this->nodeAssociations[$attrs['ID']]); // It had its opportunity
            }
            return;
        }

        // If we are in control mode, we don't fill the tree
        if ($this->controlMode) {
            return;
        }
        
        // End of control part

        // At this point, we have to finish the association resolutions brough in the array and not taken into account until now
        if (!empty($this->nodeAssociations)) {
            foreach ($this->nodeAssociations as $key => $value) {
                $this->_bindControlElement(null, array('ID' => $key), false);
                unset($this->nodeAssociations[$key]);
            }
        }
        $localElement = $this->_readData($name, $attrs);
        
        /*
         *  If a exportation node has been specified to define from where we start,
         *  we ignore the XML until being in the node specified as param,
         *  and it will finish when depth = 0
         */
        if (($this->_firstExportationNode > 0) && (!$this->_processIsParsing)) {
            if ($localElement['ID'] != $this->_firstExportationNode) {
                
                // Deactivating parsing
                $this->_processIsParsing = false;
                return;
            }
        }
        if (in_array($name, $this->tagsWithVisualTemplate)) {
            $this->inTemplateFolder = false;
        }
        $this->_processIsParsing = true;

        // Checking if it is allowed if it is the first node
        if ($this->firstNode) {
            $this->firstNode = false;
            $importRootNode = new Node($this->rootNode);
            
            // Checking depending on if first node has to be inserted or not
            // If it has to be inserted, checking if the node is allowed
            if ($this->_insertFirstNode) {
                $allowedContents = $importRootNode->GetCurrentAllowedChildren();
                $nodeType = new NodeType();
                $nodeType->SetByName($localElement['NODETYPENAME']);
                $testForAssert = !in_array($nodeType->GetID(), $allowedContents);

                //If the first node already exists and it has to be inserted, adding the suffix '_copy_n' (translated), being n>=1
                $name = FsUtils::get_name($localElement['NAME']);
                $ext = FsUtils::get_extension($localElement['NAME']);
                if ($ext != null && $ext != "") $ext = "." . $ext;
                $newName = $name . $ext;
                $index = 1;
                while ($importRootNode->GetChildByName($newName) > 0) {
                    $newName = sprintf(_("%s_copy_%d%s"), $name, $index, $ext);
                    $index++;
                }
                $localElement['NAME'] = $newName;

            } else {
                
                // If it does not have to be inserted, checking if the root node is the one specified
                $testForAssert = strcasecmp($name, $importRootNode->nodeType->get('Name'));
            }
            if ($testForAssert) {
                $this->abort = true;
                $this->messages[] = _('The init nodes of exportation and importation are not compatible, check if using the param --processFirstNode 1 is needed');
            } else {
                $localElement['OLDPARENTID'] = $localElement['PARENTID'];
                $localElement['PARENTID'] = $this->rootNode;
                $this->tree[0][0] = $localElement;
                if (!isset($this->tree[0][0]['ID'])) {
                    $this->abort = true;
                    $this->messages[] = _('Incomplete information about root node has been provided');
                }
                
                // The root node state is by default OK, and path does not have to be touch
                // In this case, the parent is the tree's one, because this is the first node
                if ($this->_insertFirstNode) {
                    
                    // ExecutedNode has to be cheated a litle bit, because it is waiting for a father
                    $parentElement = array('ID' => $localElement['PARENTID']);
                    if (in_array($localElement['NODETYPENAME'], $this->tagsForAdvancedWritting)) {
                        $this->_executeNode($localElement, $parentElement);
                    }
                } else {
                    $this->_bindNode($this->tree[0][0]['ID'], $this->rootNode, $this->tree[0][0]['PARENTID'], Constants::IMPORTED_STATUS_OK);
                }
            }
            if ($localElement['CLASS'] != 'foldernode') {
                $this->firstNode = false;
            }
            return;
        }

        // The rest of the code is for the normal case
        $this->depth++;

        // Vars used to simplify the code
        if (isset($this->tree[$this->depth]) && is_array($this->tree[$this->depth])) {
            $array = array_keys($this->tree[$this->depth]);
            $index = end($array) + 1;
        } else {
            $index = 0;
        }
        $this->tree[$this->depth][$index] = $localElement;
        $array = array_keys($this->tree[$this->depth - 1]);
        $parentElement = &$this->tree[$this->depth - 1][is_array($this->tree[$this->depth - 1])
            ? end($array)
            : 0];
        if (in_array($name, $this->tagsForAdvancedWritting)) {
            if (in_array($localElement['NODETYPENAME'], $this->templateTypes)) {
                $localElement['OLDID'] = $localElement['ID'];
                $localElement['ID'] = null;
            }
            $localElement = $this->_resolveNode($localElement);
            $this->_executeNode($localElement, $parentElement);
        }
    }

    function _resolveNode(&$node, $mode = false)
    {
        if (!isset($node['OLDPARENTID'])) {
            $node['OLDPARENTID'] = isset($node['PARENTID']) ? $node['PARENTID'] : '';
            $node['PARENTID'] = null;
        }
        $newParentId = $this->_lookForImportedNode($node['OLDPARENTID'], $mode, isset($node['NAME']) ? $node['NAME'] : '');
        if ($newParentId > 0) {
            $node['PARENTID'] = $newParentId;
        }
        if (isset($node['TARGETLINK'])) {
            $newTargetLink = $this->_lookForImportedNode($node['TARGETLINK'], $mode);
            if ($newTargetLink > 0) {
                unset($node['TARGETLINK']);
                $node['NEWTARGETLINK'] = $newTargetLink;
            }
        }
        if (isset($node['OLDID'])) {
            $newNodeId = $this->_lookForImportedNode($node['OLDID'], $mode);
            if ($newNodeId > 0) {
                $node['ID'] = $newNodeId;
                unset($node['OLDID']);
            }
        }
        return $node;
    }

    /**
     * Function to parse final elements
     *
     * @param $parser
     * @param string $name
     */
    function _endElement($parser, $name)
    {
        // If the arrived tag is the header one, we ignore it
        if ($name == Constants::HEADER) {
            return;
        }
        
        // If the tag is a control closing one, we pass to normal mode
        if (in_array($name, $this->tagsForContainControls)) {
            $this->controlMode = false;
            return;
        }
        
        // This type of tags are of advanced Writting
        if (in_array($name, $this->tagsForControl) && $this->controlMode) {
            return;
        }
        if (! $this->_processIsParsing) {
            return;
        }
        if (is_array($this->tree[$this->depth])) {
            $array = array_keys($this->tree[$this->depth]);
            $localIndex = end($array);
        } else {
            $localIndex = 0;
        }
        
        // If the tag belongs to a category of the ones which write in the element start, we finish the execution
        if ((in_array($name, $this->tagsForAdvancedWritting))) {
            unset ($this->tree[$this->depth][$localIndex]);
            $this->depth--;
            if (in_array($name, $this->tagsWithVisualTemplate)) {
                $this->inTemplateFolder = true;
            }
            return;
        }

        // We went over the whole tree, the first node was already treated in the startElement
        // (unless the first node is being processed and it is not a tag of advanced writting)
        if (($this->depth <= 0) && in_array($name, $this->tagsForAdvancedWritting)) {
            $this->abort = true;
            return;
        }

        // Isolating the current element and its father, which are the two last ones in its corresponding levels
        if (is_array($this->tree[$this->depth])) {
            $array = array_keys($this->tree[$this->depth]);
            $localIndex = end($array);
        } else {
            $localIndex = 0;
        }
        $localElement = & $this->tree[$this->depth][$localIndex];
        
        // Father element of the current one (just to simplify the rest of the function, it is not necessary)
        if ($this->depth > 0) {
            $array = array_keys($this->tree[$this->depth - 1]);
            $parentElement = &$this->tree[$this->depth - 1][is_array($this->tree[$this->depth - 1])
                ? end($array)
                : 0];
        } else {
            $parentElement = 0;
        }
        if (!empty($this->bufferedData)) {
            $localElement['DESCRIPTION'] = $this->bufferedData;
            $this->bufferedData = '';
        }
        $localElement = $this->_resolveNode($localElement);
        $this->_executeNode($localElement, $parentElement);
        unset ($this->tree[$this->depth][$localIndex]);
        $this->depth--;
    }

    /**
     * Function which manage the CDATA contents
     *
     * @param $parser
     * @param string $data
     */
    function _characterDataHandler($parser, $data)
    {
        $data = trim($data);
        if (!empty($data)) {
            $this->bufferedData = $data;
        }
    }

    function _readData($name, $attrs)
    {
        $localElement = array();
        $localElement['NODETYPENAME'] = $name;
        foreach ($attrs as $key => $value) {
            $localElement[$key] = $value;
        }
        return $localElement;
    }

    /**
     * Function where document insertion is centralized
     * Here, we should count how many nodes were successfully or unsuccessfully inserted
     *
     * @param array $elementToInsert
     * @param bool $mode //not in use yet, but it will define if it is being executed from the beginning o from the process end
     * @return int (inserted node)
     */
    function _insertElement($elementToInsert)
    {
        $dbObj = new Ximdex\Runtime\Db();
        $query = sprintf("SELECT IdImportationNode"
            . " FROM XimIONodeTranslations"
            . " WHERE IdExportationNode = %d",
            $elementToInsert['ID']);
        $dbObj->Query($query);
        if ($dbObj->numRows == 1) {
            $idImportationNode = $dbObj->GetValue('IdImportationNode');
            if ($idImportationNode > 0) {
                return $idImportationNode;
            }
        }
        
        /*
         * If we do not want a recursive importation, we should ignore all the insertions in a depth different from 0,
         * moment in which we will have all the information to insert the node
        */
        $baseIO = new Ximdex\IO\BaseIO();
        $idUser = Ximdex\Runtime\Session::get("userID");
        if (! $idUser) {
            $this->abort = true;
            $this->messages[] = _('No valid user to perform the importation found');
        }
        if ($this->heuristicMode) {
            $idImportationNode = $baseIO->check($elementToInsert, $idUser);
        } else {
            $idImportationNode = $baseIO->build($elementToInsert, $idUser);
        }
        if ($idImportationNode < 0) {
            Logger::error('Error inserting the node: ' . $elementToInsert['ID']);
            foreach ($baseIO->messages->messages as $message) {
                Logger::error($message['message']);
            }
        } elseif ($idImportationNode > 0) {
            $status = 1;
        } else {
            $status = $idImportationNode;
        }

        // TODO $path is not in use (sure?)
        $this->_bindNode($elementToInsert['ID'], $idImportationNode, $elementToInsert['OLDPARENTID'], $status);
        if (! $idImportationNode) {
            $this->processedNodes['failed'][$idImportationNode]++;
            $path = isset($elementToInsert['CHILDRENS']) && is_array($elementToInsert['CHILDRENS'])
                ? $this->_getPath($elementToInsert['CHILDRENS'])
                : '';
        } else {
            $dbObj = new Ximdex\Runtime\Db();
            
            // Suggesting the packages to launch after this one
            $query = sprintf("SELECT xe.timeStamp"
                . " FROM XimIONodeTranslations xnt"
                . " INNER JOIN XimIOExportations xe ON xnt.IdXimioExportation = xe.IdXimioExportation"
                . " 	AND xe.idXimio = %d AND xe.timeStamp != %s"
                . " WHERE xnt.IdExportationParent = %d"
                . " AND xnt.status < 0",
                $this->idXimio,
                $dbObj->sqlEscapeString($this->timeStamp),
                $elementToInsert['OLDPARENTID']);
            $dbObj->Query($query);
            while (!$dbObj->EOF) {
                $timeStamp = $dbObj->GetValue('timeStamp');
                if (!in_array($timeStamp, $this->sugestedPackages)) {
                    $this->sugestedPackages[] = $timeStamp;
                }
                $dbObj->Next();
            }

            // Counting as successfully inserted node
            $this->processedNodes['success']++;
            if (!empty($this->pendingNodes[$elementToInsert['ID']])) {
                foreach ($this->pendingNodes[$elementToInsert['ID']] as $children) {
                    $children = $this->_resolveNode($children);
                    $this->_insertElement($children);
                }
                
                // Once a node sons have been processed, deleting this part from the array
                unset($this->pendingNodes[$elementToInsert['ID']]);
            }
        }
        $status = $idImportationNode > 0 ? Constants::IMPORTED_STATUS_OK : $idImportationNode;
        if ($status === 1) {
            if (isset($elementToInsert['STATE']) && ($elementToInsert['STATE'] == Constants::PUBLISH_STATUS)) {
                $status = Constants::IMPORTED_STATUS_OK_TO_PUBLISH;
            }
        }
        $path = isset($elementToInsert['CHILDRENS']) && is_array($elementToInsert['CHILDRENS']) ?
            $this->_getPath($elementToInsert['CHILDRENS']) : '';
        $this->_bindNode($elementToInsert['ID'], $idImportationNode, $elementToInsert['OLDPARENTID'], $status, $path);
        $this->idfinal = $idImportationNode;
        return $idImportationNode;
    }

    /**
     * Associating a "control" element (such as channels, languages or groups) to a Ximdex element where it is going to be imported
     *
     * @param string $name control name
     * @param array $attrs control attributes
     */
    function _bindControlElement($name, $attrs, $checkMode = true)
    {
        if (isset($this->nodeAssociations[(int)$attrs['ID']])) {
            $finalNode = $this->nodeAssociations[(int)$attrs['ID']];
            
            // If the node is not of the specified type, we ignore it
            if ($checkMode) {
                $node = new Node($finalNode);
                if (strcasecmp($name, $node->nodeType->GetName())) {
                    return false;
                }
            } else {
                $attrs['PARENTID'] = 0; // hopping this value will not give problems
            }
        } else {
            
            // If it is the same ximIO, the control element is associated automatically
            if ($this->idLocalXimio == $this->idXimio) {
                $finalNode = (int)$attrs['ID'];
            } else {
                
                // This case is supposed not to happen, but, just in case, we cancel the process to avoid inconsistences.
                return false;
            }
        }
        
        // State of a control element always correct
        $this->_bindNode((int)$attrs['ID'], $finalNode, $attrs['PARENTID'], Constants::IMPORTED_STATUS_OK);
        return true;
    }

    /**
     * Associating a Ximdex document exportation identifier to an importation one
     * There are two possible nodes depending on the object state, in memory mode (heuristic)
     * and database mode (for a second step)
     *
     * @param int $exportationNode Identifier of the node which is being exported
     * @param int $importationNode Identifier of the node which is being imported
     * @param int $parentId Identifier of the exportation node's father
     * @param int $status Node importation status
     * @param string $path Path to file if it exists
     */
    function _bindNode($exportationNode, $importationNode, $parentId, $status, $path = '')
    {
        if ($this->heuristicMode) {
            $this->bindedNodes[$exportationNode] = $importationNode;
        } else {
            $dbObj = new Ximdex\Runtime\Db();
            $query = sprintf("SELECT IdNodeTranslation"
                . " FROM XimIONodeTranslations"
                . " WHERE IdExportationNode = %d AND IdXimioExportation = %d",
                $exportationNode, $this->importationId);
            $dbObj->Query($query);
            if ($dbObj->numRows > 0) {
                $idNodeTranslation = $dbObj->GetValue('IdNodeTranslation');
            }
            if (isset($idNodeTranslation) && $idNodeTranslation > 0) {
                $dbObj = new Ximdex\Runtime\Db();
                $query = sprintf("UPDATE XimIONodeTranslations"
                    . " SET IdImportationNode = %d, IdExportationParent = %d, status = %d, path = %s"
                    . " WHERE IdNodeTranslation = %d",
                    $importationNode,
                    $parentId,
                    $status,
                    $dbObj->sqlEscapeString(empty($path) ? '' : $path), //needed, due to the temporal meassure of db.inc
                    $idNodeTranslation);
                $dbObj->Execute($query);
            } else {
                $dbObj = new Ximdex\Runtime\Db();
                $query = sprintf("INSERT INTO XimIONodeTranslations"
                    . " (IdExportationNode, IdExportationParent, IdImportationNode, IdXimioExportation, status, path)"
                    . " VALUES (%d, %d, %d, %d, %d, %s)",
                    $exportationNode, $parentId, $importationNode,
                    $this->importationId, $status, $dbObj->sqlEscapeString(empty($path) ? '' : $path));
                $dbObj->Execute($query);
            }
        }
    }

    /**
     * Function which encapsulate the search of a father node, it can be based on a memory array or in a database query,
     * depending on the work mode in which the object is
     *
     * @param int $idNode
     * @return int
     */
    function _lookForImportedNode($idNode, $finalStageMode = false, $nodeName = '')
    {
        if ($this->heuristicMode) {
            return isset($this->bindedNodes[$idNode]) ? $this->bindedNodes[$idNode] : null;
        }
        if ($idNode > 0) {
            $dbObj = new Ximdex\Runtime\Db();
            $query = sprintf("SELECT xnt.IdImportationNode FROM XimIONodeTranslations xnt"
                . " INNER JOIN XimIOExportations xe ON xnt.IdXimioExportation = xe.idXimIOExportation"
                . " WHERE xnt.IdExportationNode = %d"
                . " AND xnt.status = %d"
                . " AND xe.idXimio = %d",
                $idNode, Constants::IMPORTED_STATUS_OK, $this->idXimio);
            $dbObj->Query($query);
            if (!$dbObj->EOF) {
                $value = $dbObj->GetValue('IdImportationNode');
                if (!$finalStageMode) {
                    return $value;
                }
                // If finalStageMode
                if ($value > 0) {
                    return $value;
                }
            }
            if (($this->mode == IMPORT_MODE || empty($nodeName)) && !$finalStageMode) {
                return null;
            } else {
                $dbObj = new Ximdex\Runtime\Db();
                
                /**
                 * Mode == COPY_MODE, we still have to check if the node is already existing in the interface, thus 
                 * the existing node is going to be returned
                 */
                $query = sprintf("SELECT xnt2.IdImportationNode"
                    . " FROM XimIONodeTranslations xnt1 INNER JOIN XimIONodeTranslations xnt2"
                    . " ON xnt1.IdExportationParent = xnt2.IdExportationNode"
                    . " WHERE xnt1.IdExportationNode = %d",
                    $idNode);
                if ($dbObj->numRows > 0) {
                    $parentNode = $dbObj->GetValue('IdImportationNode');
                    $node = new Node($parentNode);
                    $childrens = $node->GetChildren();
                    foreach ($childrens as $children) {
                        if (!strcmp($nodeName, $children->get('Name'))) {
                            return $children;
                        }
                    }
                }
                if ($finalStageMode) {
                    $node = new Node($idNode);
                    if ($node->get('IdNode') > 0) {
                        return $idNode;
                    }
                }
                return null;
            }
        } else {
            $idParent = null;
        }
        return $idParent;
    }

    /**
     * Function in which element processing is delegated, depending on type/state of the element to process
     * It can be inserted, stored in a buffer of pending elements associated to a concrete node that will be processed
     * when this concrete node would be processed and an identifier would be obtained, or directly if it is not a processable node
     * it will be sotred in the father object as a son and baseIO will split this array
     * (delegating in baseIO for nodetype details)
     *
     * Exceptions: For reasons of element acknowledgement, the visualtemplate should go contained inside its corresponding container
     *        to not being treated as a descriptor node
     *
     * @param array $localElement
     * @param array & $parentElement
     */
    function _executeNode($localElement, & $parentElement)
    {
        $isTemplateInDocument = ((in_array($localElement['NODETYPENAME'], $this->templateTypes)) && !$this->inTemplateFolder);
        $isControlElement = in_array($localElement['NODETYPENAME'], $this->tagsForControl);
        if ($isTemplateInDocument || $isControlElement) {
            $tagIsInfo = true;
        } else {
            $tagIsInfo = false;
        }
        if (isset($localElement['NODETYPENAME']) && in_array($localElement['NODETYPENAME'], $this->relations)) {
            $this->pendingRelations[] = $localElement;
            return;
        }
        if (isset($localElement['CLASS']) && isset($localElement['NAME']) && !$tagIsInfo) {
            if ((isset($localElement['PARENTID']) && ((int)$localElement['PARENTID'] > 0)) && !isset($localElement['TARGETLINK'])) {
                $this->_insertElement($localElement);
            } else {
                if (isset($localElement['TARGETLINK'])) {
                    
                    // If we do no have a definitive identifier for the father, we push it to the tail
                    $this->pendingNodes[$localElement['TARGETLINK']][] = $localElement;
                } else {
                    $this->pendingNodes[$parentElement['ID']][] = $localElement;
                }
            }
        } else {
            
            // If it is a control tag and it has correspondence with the new ximdex, we assign the imporatation identifier,
            // if if has not, it will be deleted and later baseIO should take care of exceptions (such as not having languages or channels)
            if (isset($localElement['NODETYPENAME']) && ($isControlElement 
                || in_array($localElement['NODETYPENAME'], $this->templateTypes))) {
                if ($isTemplateInDocument && $this->mode == IMPORT_MODE) {
                    $newId = $this->_resolveTemplate($localElement, $parentElement);
                } else {
                    $newId = $this->_lookForImportedNode($localElement['ID'], false, $localElement['NAME']);
                }
                if ($newId) {
                    $localElement['ID'] = $newId;
                } else {
                    if ($this->mode != COPY_MODE) {
                        $localElement['OLDID'] = $localElement['ID']; // A null identifier is assigned to the element
                        $localElement['ID'] = null;
                    }
                }
            }
            $parentElement['CHILDRENS'][] = $localElement;
        }
    }

    function _resolveTemplate($template, $parentElement)
    {
        // First, checking if it is resolved or if it can be
        $firstTry = $this->_lookForImportedNode($template['ID'], false, $template['NAME']);
        if ($firstTry > 0) {
            return $firstTry;
        }
        if (!(($template['ID'] > 0) && ($parentElement['ID'] > 0))) {
            $this->messages->add(_('No se ha podido resolver el template'), MSG_TYPE_WARNING);
            return null;
        }

        // Taking the father of parentElement, because the parentElement[id] is not resolved yet
        // Calculating the project of the XML document
        // TODO we may put here a function to search a node already imported, if it is not imported, it is getting difficult
        if (isset($parentElement['OLDPARENTID']) && ($parentElement['PARENTID'] > 0)) {
            $idParentOfParentElement = $parentElement['PARENTID'];
        } else {
            $idParentToStimate = 0;
            do {
                if (!(isset($idParentToStimate) && !is_null($idParentToStimate))) {
                    $idParentToStimate = $parentElement['PARENTID'];
                } else {
                    $dbObj = new Ximdex\Runtime\Db();
                    $query = sprintf("SELECT IdExportationParent"
                        . " FROM XimIONodeTranslations"
                        . " WHERE IdExportationNode = %s",
                        $dbObj->sqlEscapeString($idParentToStimate));
                    $dbObj->query($query);
                    if ($dbObj->numRows > 0) {
                        $idParentToStimate = $dbObj->getValue('IdExportationParent');
                    }
                }
                $dbObj = new Ximdex\Runtime\Db();
                $query = sprintf("SELECT IdImportationNode, IdExportationParent"
                    . " FROM XimIONodeTranslations"
                    . " WHERE IdExportationNode = %s",
                    $idParentToStimate);
                $dbObj->query($query);
                if ($dbObj->numRows > 0) {
                    $idParentOfParentElement = $dbObj->getValue('IdImportationNode');
                    $idParentToStimate = $dbObj->getValue('IdExportationParent');
                } else {
                    $parent = $this->_searchParentInTree($idParentToStimate);
                    $idParentOfParentElement = -2;
                    $idParentToStimate = $parent;
                }
            } while (!is_null($idParentOfParentElement) && ($idParentOfParentElement > 0));
        }
        $parentNode = new Node($idParentOfParentElement);
        $idParentProject = null;
        if ($parentNode->get('IdNode') > 0) {
            $idParentProject = $parentNode->getProject();
        }
        $newId = $this->_lookForImportedNode($template['ID'], false, $template['NAME']);
        $idLocalProject = null;
        
        // Looking for one in the ximio equivalence table
        if ($newId > 0) {
            
            //Calculating the project of the estimated pvd
            $node = new Node($newId);
            if ($node->get('IdNode') > 0) {
                $idLocalProject = $node->getProject();
            }
            if ((!empty($idParentProject) && !empty($idLocalProject)) && ($idLocalProject == $idParentProject)) {
                return $newId;
            }
        }

        // If no we look for one with same UUID
        $idLocalProject = null;
        if (isset($template['UUID'])) {
            $newId = $this->_lookForUUID($template['UUID']);
        }
        if (isset($newId) && $newId > 0) {
            $node = new Node($newId);
            if ($node->get('IdNode') > 0) {
                $idLocalProject = $node->getProject();
            }
            if (!empty($idLocalProject) && ($idLocalProject > 0)) {
                return $newId;
            }
        }
        
        // Trying to insert the node at least, looking for a parentid and we insert it
        $parentProject = new Node($idParentProject);
        if ($parentProject->get('IdNode') > 0) {
            $idximPvdNode = $parentProject->GetChildByName('schemas');
            $template['PARENTID'] = $idximPvdNode;
            $baseIO = new Ximdex\IO\BaseIO();
            $idUser = Ximdex\Runtime\Session::get('userID');
            if ($this->heuristicMode) {
                $result = $baseIO->check($template, $idUser);
            } else {
                $result = $baseIO->build($template, $idUser);
            }
            $status = $result > 0 ? 1 : $result;
            $path = is_array($template['CHILDRENS']) ? $this->_getPath($template['CHILDRENS']) : '';
            $elementToInsert = array();
            $this->_bindNode($template['ID'], $result, $elementToInsert['NULL'], $status, $path);
        }
        Logger::warning('No pvd can be successfully estimated for the node ' . $parentElement['ID']);
        return null;
    }

    function _searchParentInTree($idNode)
    {
        foreach ($this->tree as $dept) {
            foreach ($dept as $element) {
                if (isset($element['OLDID']) && $element['OLDID'] == $idNode) {
                    return $element['OLDPARENTID'];
                }
                if (!isset($element['OLDID']) && isset($element['ID']) && $element['ID'] == $idNode) {
                    return $element['PARENTID'];
                }
            }
        }
        return null;
    }

    function _lookForUUID($uuid)
    {
        $db = new Ximdex\Runtime\Db();
        $query = sprintf("SELECT IdNode From NodeProperties"
            . " WHERE Property = 'UUID' AND Value = %s", $db->sqlEscapeString($uuid));
        $db->query($query);
        if (!($db->numRows > 0)) {
            return null;
        }
        if ($db->numRows == 1) {
            return $db->getValue('IdNode');
        }
        Logger::error('An inconsistency was found in database: several UUID in NodeProperties table with the same value');
        return null;
    }

    /**
     * Looking for a node value in the sons (children)
     *
     * @param array $childrens
     * @param string $nodeName
     * @return mixed
     */
    function _getValueFromChildren($childrens, $nodeName)
    {
        if (!is_array($childrens)) {
            return false;
        }
        if (empty($nodeName)) {
            return false;
        }
        $attrValues = array();
        foreach ($childrens as $children) {
            if (!is_array($children)) {
                continue;
            }
            foreach ($children as $attrKey => $attrValue) {
                if (!strcmp($attrKey, $nodeName)) $attrValues[] = $attrValue;
            }
        }
        return $attrValues;
    }

    /**
     * Returns a node path
     *
     * @param array $childrens
     * @return string
     */
    function _getPath($childrens)
    {
        if (is_array($childrens)) {
            $paths = $this->_getValueFromChildren($childrens, 'SRC');
            if (is_array($paths) && count($paths) == 1) {
                $path = $paths[0];
            }
        }
        return !empty($path) ? $path : '';
    }

    function _insertImportationInfo($idXimio, $timeStamp)
    {
        if (($this->mode != COPY_MODE) && empty($timeStamp)) {
            return false;
        }
        $dbObj = new Ximdex\Runtime\Db();
        $query = sprintf("SELECT idXimIOExportation FROM XimIOExportations"
            . " WHERE idXimIO = %d AND timeStamp = %s",
            $idXimio, $dbObj->sqlEscapeString($timeStamp));
        $dbObj->Query($query);
        $idXimioExportation = $dbObj->numRows == 1 ? $dbObj->GetValue('idXimIOExportation') : null;
        if ($idXimioExportation > 0) {
            return $idXimioExportation;
        }
        $dbObj = new Ximdex\Runtime\Db();
        $query = sprintf("INSERT into XimIOExportations"
            . " (idXimio, timeStamp)"
            . " VALUES"
            . " (%s, %s)",
            $dbObj->sqlEscapeString($idXimio),
            $dbObj->sqlEscapeString($timeStamp));
        $dbObj->Execute($query);
        $insertedId = $dbObj->newID;
        if ($insertedId > 0) {
            return $insertedId;
        }
        return false;
    }
}
