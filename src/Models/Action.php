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

namespace Ximdex\Models;

use Ximdex\Models\ORM\ActionsOrm;

class Action extends ActionsOrm
{
    /**
     * ID of the current action
     */
    public $ID;
    
    /**
     * DB object used in methods
     */
    public $dbObj;
    
    /**
     * Shows if there was an error
     */
    public $flagErr = false;
    
    /**
     * Error code
     */
    public $numErr;
    
    /**
     * Error message
     */
    public $msgErr;
    
    /**
     * Class error list
     * 
     * @var array
     */
    public $errorList = array(
        1 => 'Database connection error',
        2 => 'Action does not exist'
    );
    
    public $_fieldsToTraduce = array('Name', 'Description');
    
    public $autoCleanErr = true;

    public function __construct(int $actionID = null)
    {
        $this->flagErr = false;
        $this->autoCleanErr = true;
        parent::__construct($actionID);
    }
    
    /**
     * Get an array with actions without permissions required
     * 
     * @return array Array with actions name
     */
    public static function getAlwaysAllowedActions()
    {
        return array('browser3', 'composer', 'welcome', 'infonode', 'changelang', 'rendernode');
    }

    /**
     * Returns an arry with the ids of all the system actions
     * 
     * @return array|null of ActionID
     */
    public function getAllActions()
    {
        $sql = 'SELECT IdAction FROM Actions';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = array();
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->getValue('IdAction');
            $dbObj->Next();
        }
        return $salida;
    }

    /**
     * Returns an array with the ids of all the action of a nodetype
     * 
     * @param int $code
     * @param string $msg
     */
    public function setError(int $code, string $msg = null)
    {
        $this->flagErr = TRUE;
        $this->numErr = $code;
        $this->msgErr = ($msg != null) ? $msg : $this->errorList[$code];
    }

    /**
     * Returns the nodetype id assocaited with the current action
     * 
     * @param int $nodeType
     * @param bool $includeActionsWithNegativeSort
     * @return array|null
     */
    public function GetActionListOnNodeType(int $nodeType = null, bool $includeActionsWithNegativeSort = false)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        if (! $includeActionsWithNegativeSort) {
            $sql = sprintf('SELECT IdAction FROM Actions WHERE idNodeType = %d AND Sort > 0 ORDER BY Sort ASC', $nodeType);
        } else {
            $sql = sprintf('SELECT IdAction FROM Actions WHERE idNodeType = %d ORDER BY Sort ASC', $nodeType);
        }
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = null;
        while (! $dbObj->EOF) {
            $salida[] = $dbObj->getValue('IdAction');
            $dbObj->Next();
        }
        return $salida ? $salida : NULL;
    }

    /**
     * Returns the current action id
     * 
     * @return bool|string
     */
    public function getNodeType()
    {
        return $this->IdNodeType;
    }

    /**
     * Changes the current action id
     * 
     * @return bool|string
     */
    public function getID()
    {
        return $this->IdAction;
    }

    /**
     * Returns the current action name
     * 
     * @param int $actionID
     * @return bool|null|string
     */
    public function setID(int $actionID)
    {
        parent::__construct($actionID);
        if (! $this->IdAction) {
            $this->setError(2);
            return null;
        }
        return $this->IdAction;
    }

    /**
     * Changes the current action name
     * 
     * @param $name
     * @return bool|string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Returns the current action description
     * 
     * @param $name
     * @return bool|int|null|string
     */
    public function setName(string $name)
    {
        if (! $this->IdAction) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Change the current action description
     * 
     * @param $description
     * @return bool|string
     */
    public function getDescription()
    {
        return $this->Description;
    }

    public function setDescription(string $description = null)
    {
        if (! $this->IdAction) {
            $this->SetError(2, _('Action does not exist'));
            return false;
        }
        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current action command
     * 
     * @return string (command)
     */
    public function getCommand()
    {
        return $this->Command;
    }

    /**
     * Changes the current action command
     * 
     * @param string $command
     * @return  bool|int (status)
     */
    public function setCommand(string $command)
    {
        if (! $this->IdAction) {
            $this->SetError(2, _('Action does not exist'));
            return false;
        }
        $result = $this->set('Command', $command);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current action order
     * 
     * @return string (command)
     */
    public function getSort()
    {
        return $this->Sort;
    }

    /**
     * Changes the current action order
     * 
     * @param int $sort
     * @return int (status)
     */
    public function setSort(int $sort)
    {
        if (! $this->IdAction) {
            $this->SetError(2, _('Action does not exist'));
            return false;
        }
        $result = $this->set('Sort', $sort);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Returns the current action icon
     * 
     * @return string (icon)
     */
    public function getIcon()
    {
        return $this->Icon;
    }

    /**
     * Returns if the given user can execute the action in a given node
     * Changes the current action icon
     * 
     * @param $icon
     * @return bool|int (status)
     */
    public function setIcon(string $icon)
    {
        if (! $this->IdAction) {
            $this->SetError(2, _('Action does not exist'));
            return false;
        }
        $result = $this->set('Icon', $icon);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * Creates a new action and load its id in the class actionID
     * 
     * @param $actionID
     * @param $nodeType
     * @param $name
     * @param $command
     * @param $icon
     * @param $description
     * @return string ActionID - loaded as a attribute
     */
    public function createNewAction(int $actionID, int $nodeType, string $name, string $command, string $icon = null, string $description = null)
    {
        $this->set('IdAction', $actionID);
        $this->set('IdNodeType', $nodeType);
        $this->set('Name', $name);
        $this->set('Command', $command);
        $this->set('Icon', $icon);
        $this->set('Description', $description);
        $this->ID = $this->add();
        return $this->ID;
    }

    /**
     * Delete current action
     */
    public function deleteAction()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf('DELETE FROM RelRolesActions WHERE IdAction = %d', $this->ID);
        $dbObj->Execute($query);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
        }
        $this->delete();
        $this->ID = null;
    }

    public function getModule()
    {
        return $this->Module;
    }

    public function setAutoCleanOn()
    {
        $this->autoCleanErr = true;
    }

    public function setAutoCleanOff()
    {
        $this->autoCleanErr = false;
    }

    public function hasError()
    {
        $error = $this->flagErr;
        if ($this->autoCleanErr) {
            $this->clearError();
        }
        return $error;
    }

    /**
     * Returns true if there was an error in the class
     */
    public function clearError()
    {
        $this->flagErr = false;
    }

    public function setByCommandAndModule(string $name, int $idNode, string $module = null)
    {
        $node = new Node($idNode);
        $idNodeType = $node->getNodeType();
        if (! $module) {
            return $this->setByCommand($name, $idNodeType);
        } else {
            $result = $this->find('IdAction', 'Command = %s AND IdNodeType = %s AND Module = %s', array($name, $idNodeType, $module), MONO);
            if (count($result) != 1) {
                return 0;
            }
            $this->__construct($result[0]);
            return $this->IdAction;
        }
    }

    public function setByCommand(string $name, int $idNodeType)
    {
        $result = $this->find('IdAction', 'Command = %s AND IdNodeType = %s', array($name, $idNodeType), MONO);
        if (count($result) != 1) {
            return false;
        }
        $this->__construct($result[0]);
        return $this->IdAction;
    }
}
