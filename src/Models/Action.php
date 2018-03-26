<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 27/1/16
 * Time: 11:34
 */

namespace Ximdex\Models;


use Ximdex\Models\ORM\ActionsOrm;

class Action extends ActionsOrm
{


    /**
     * ID of the current action.
     */
    var $ID;
    /**
     * DB object used in methods.
     */
    var $dbObj;
    /**
     * Shows if there was an error.
     */
    var $flagErr;
    /**
     * Error code
     */

    var $numErr;
    /**
     * Error message
     */
    var $msgErr;
    /**
     * Class error list.
     */
    var $errorList = array(
        1 => 'Database connection error',
        2 => 'Action does not exist'
    );
    var $_fieldsToTraduce = array('Name', 'Description');

    var $autoCleanErr = false;

    /**
     * Get an array with actions without permissions required.
     * @return array Array with actions name.
     */
    public static function getAlwaysAllowedActions()
    {
        return array("browser3", "composer", "welcome", "infonode", "changelang", 'rendernode');
    }

    /**
     * Returns an arry with the ids of all the system actions.
     * @return array of ActionID
     */
    function GetAllActions()
    {
        $salida = array();
        $sql = "SELECT IdAction FROM Actions";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->GetValue("IdAction");
            $dbObj->Next();
        }
        return $salida;
    }

    /**
     * Returns an array with the ids of all the action of a nodetype.
     */

    /**
     * @param $code
     * @param null $msg
     */
    function SetError($code, $msg = null)
    {
        $this->flagErr = TRUE;
        $this->numErr = $code;
        $this->msgErr = ($msg != null) ? $msg : $this->errorList[$code];
    }

    /**
     * Returns the nodetype id assocaited with the current action.
     */

    /**
     * @param null $nodeType
     * @param bool $includeActionsWithNegativeSort
     * @return array|null
     */
    function GetActionListOnNodeType($nodeType = NULL, $includeActionsWithNegativeSort = false)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        if (!$includeActionsWithNegativeSort) {
            $sql = sprintf("SELECT IdAction FROM Actions WHERE idNodeType = %d AND Sort > 0 ORDER BY Sort ASC", $nodeType);
        } else {
            $sql = sprintf("SELECT IdAction FROM Actions WHERE idNodeType = %d ORDER BY Sort ASC", $nodeType);
        }
        $dbObj->Query($sql);
        if ($dbObj->numErr != 0) {
            $this->SetError(1);
            return null;
        }
        $salida = null;
        while (!$dbObj->EOF) {
            $salida[] = $dbObj->GetValue("IdAction");
            $dbObj->Next();
        }
        return $salida ? $salida : NULL;
    }

    /**
     * Returns the current action id.
     */

    /**
     * @return bool|string
     */
    function GetNodeType()
    {
        return $this->get('IdNodeType');
    }

    /**
     * Changes the current action id.
     */

    /**
     * @return bool|string
     */
    function GetID()
    {
        return $this->get('IdAction');
    }

    /**
     *  Returns the current action name.
     */

    /**
     * @param $actionID
     * @return bool|null|string
     */
    function SetID($actionID)
    {
        parent::__construct($actionID);
        if (!($this->get('IdAction') > 0)) {
            $this->SetError(2);
            return null;
        }
        return $this->get('IdAction');
    }

    /**
     * Changes the current action name.
     * @param $name
     */

    /**
     * @return bool|string
     */
    function GetName()
    {
        return $this->get('Name');
    }

    /**
     *  Returns the current action description.
     */

    /**
     * @param $name
     * @return bool|int|null|string
     */
    function SetName($name)
    {
        if (!($this->get('IdAction') > 0)) {
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
     * Change the current action description.
     * @param $description
     */

    /**
     * @return bool|string
     */
    function GetDescription()
    {
        return $this->get('Description');
    }

    /**
     * @param $description
     * @return bool|int|null|string
     */
    function SetDescription($description)
    {
        if (!($this->get('IdAction') > 0)) {
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
     * Returns the current action command.
     * @return string (command)
     */
    function GetCommand()
    {
        return $this->get('Command');
    }

    /**
     * Changes the current action command.
     * @param $command
     * @return  int (status)
     */
    function SetCommand($command)
    {
        if (!($this->get('IdAction') > 0)) {
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
     * Returns the current action order.
     * @return string (command)
     */
    function GetSort()
    {
        return $this->get('Sort');
    }

    /**
     * Changes the current action order.
     * @param $sort
     * @return int (status)
     */
    function SetSort($sort)
    {
        if (!($this->get('IdAction') > 0)) {
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
     * Returns the current action icon.
     * @return string (icon)
     */
    function GetIcon()
    {
        return $this->get('Icon');
    }

    /**
     * Returns if the given user can execute the action in a given node.
     */

    /**
     * Changes the current action icon.
     * @param $icon
     * @return int (status)
     */
    function SetIcon($icon)
    {
        if (!($this->get('IdAction') > 0)) {
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
     * @param $userID
     * @param $nodeID
     */
    function CheckAccessPermissionOnNode($userID, $nodeID)
    {

    }

    /**
     * Creates a new action and load its id in the class actionID.
     * @param $actionID
     * @param $nodeType
     * @param $name
     * @param $command
     * @param $icon
     * @param $description
     * @return string ActionID - loaded as a attribute
     */
    function CreateNewAction($actionID, $nodeType, $name, $command, $icon, $description)
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
     * Delete current action.
     * @return int (status)
     */
    function DeleteAction()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $query = sprintf("DELETE FROM RelRolesActions WHERE IdAction= %d", $this->ID);
        $dbObj->Execute($query);
        if ($dbObj->numErr != 0)
            $this->SetError(1);

        $this->delete();
        $this->ID = null;
    }

    /**
     * @return bool|string
     */
    function GetModule()
    {
        return $this->get('Module');
    }

    /**
     *
     */
    function SetAutoCleanOn()
    {
        $this->autoCleanErr = TRUE;
    }

    /**
     *
     */
    function SetAutoCleanOff()
    {
        $this->autoCleanErr = FALSE;
    }
    /**
     * Loads an errorin the class.* @param $code
     */

    /**
     * @return mixed
     */
    function HasError()
    {
        $aux = $this->flagErr;
        if ($this->autoCleanErr)
            $this->ClearError();
        return $aux;
    }

    /**
     * Returns true if there was an error in the class.
     */

    /**
     *
     */
    function ClearError()
    {
        $this->flagErr = FALSE;
    }

    /**
     * @param $name
     * @param $idNode
     * @param null $module
     * @return bool|int|string
     */
    function setByCommandAndModule($name, $idNode, $module = null)
    {
        $node = new \Ximdex\Models\Node($idNode);
        $idNodeType = $node->GetNodeType();

        if ($module == NULL) {
            return $this->setByCommand($name, $idNodeType);
        } else {
            $result = $this->find('IdAction', 'Command = %s AND IdNodeType = %s AND Module = %s',
                array($name, $idNodeType, $module), MONO);
            if (count($result) != 1) {
                return 0;
            }
            $this->Action($result[0]);
            return $this->get('IdAction');
        }

    }

    /**
     * @param $name
     * @param $idNodeType
     * @return bool|string
     */
    function setByCommand($name, $idNodeType)
    {
        $result = $this->find('IdAction', 'Command = %s AND IdNodeType = %s',
            array($name, $idNodeType), MONO);
        if (count($result) != 1) {
            return false;
        }
        $this->Action($result[0]);
        return $this->get('IdAction');
    }

    /**
     * @param null $actionID
     */
    function Action($actionID = null)
    {
        $this->flagErr = FALSE;
        $this->autoCleanErr = TRUE;
        $errorlist[1] = _('Database connection error');
        $errorlist[2] = _('Action does not exist');

        parent::__construct($actionID);
    }

}