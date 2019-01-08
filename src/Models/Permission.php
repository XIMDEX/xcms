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

namespace Ximdex\Models;

use Ximdex\Models\ORM\PermissionsOrm;

class Permission extends PermissionsOrm
{
    public $permissionID;
    
    /**
     * Error code
     * 
     * @var int
     */
    public $numErr;
    
    /**
     * Error message
     * 
     * @var string
     */
    public $msgErr;
    
    /**
     * Class error list
     * 
     * @var array
     */
    public $errorList = array();
    
    public $_fieldsToTraduce = array('Description');

    /**
     * Constructor
     * 
     * @param int $_params
     */
    function __construct(int $params = null)
    {
        $this->errorList[1] = _('Database connection error');
        $this->errorList[2] = _('Permit does not exist');
        $this->errorList[3] = _('Not initialized object');
        parent::__construct($params);
    }

    public function getAllPermissions()
    {
        $this->clearError();
        $sql = 'SELECT IdPermission FROM Permissions';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        if (! $dbObj->numErr) {
            $result = array();
            while (!$dbObj->EOF) {
                $result[] = $dbObj->GetValue('IdPermission');
                $dbObj->Next();
            }
            return $result;
        }
        $this->SetError(1);
        return false;
    }

    /**
     * Returns the idPermission of an object
     * 
     * @return int
     */
    public function getID()
    {
        return $this->get('IdPermission');
    }

    /**
     * Allows as to change the object idPermission. This avoid the have to destroy and re-create
     * 
     * @param int $id
     * @return NULL|boolean|string
     */
    public function setID(int $id = null)
    {
        $this->ClearError();
        parent::__construct($id);
        if (! $this->get('IdPermission')) {
            $this->SetError(2);
            return null;
        }
        return $this->get('IdPermission');
    }

    public function setByName(string $name)
    {
        $this->ClearError();
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query(sprintf('SELECT IdPermission FROM Permissions WHERE Name = %s', $dbObj->sqlEscapeString($name)));
        if ($dbObj->numRows) {
            $this->SetID($dbObj->GetValue('IdPermission'));
        } else {
            $this->permissionID = null;
            $this->SetError(2);
        }
    }

    /**
     * Returns the user name associated to an idPermission
     * 
     * @return boolean|string
     */
    public function getName()
    {
        return $this->get('Name');
    }

    /**
     * Updates the database with the new permit name
     * 
     * @param string $name
     * @return boolean|number|NULL|string
     */
    public function SetName(string $name)
    {
        $this->clearError();
        if (! $this->get('IdPermission')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Name', $name);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    public function getDescription()
    {
        return $this->get('Description');
    }

    public function setDescription(string $description)
    {
        $this->clearError();
        if (! $this->get('IdPermission')) {
            $this->SetError(2);
            return false;
        }
        $result = $this->set('Description', $description);
        if ($result) {
            return $this->update();
        }
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\Data\GenericData::add()
     */
    public function add()
    {
        $this->CreateNewPermission($this->get('Name'));
    }

    /**
     * Creates a new permit if this not exist in the database and loads its idPermission
     * 
     * @param string $name
     * @param int $pID
     * @return int|bool
     */
    public function createNewPermission(string $name, int $pID = null)
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query('SELECT IdPermission FROM Permissions WHERE Name = %s', $dbObj->sqlEscapeString($name));
        if (! $dbObj->numRows) {
            $this->set('Name', $name);
            if (! empty($pID)) {
                $this->set('IdPermission', $pID);
            }
            parent::add();
            return $this->get('IdPermission');
        }
        return false;
    }

    public function delete()
    {
        $this->deletePermission();
    }

    /**
     * Deletes the current permit
     */
    public function deletePermission()
    {
        $this->clearError();
        if ($this->get('IdPermission')) {
            parent::delete();
        } else {
            $this->SetError(3);
        }
    }

    public function addRole(int $rID)
    {
        $myrole = new Role($rID);
        $myrole->addPermission($this->get('IdPermission'));
    }

    public function deleteRole(int $rID)
    {
        $myrole = new Role($rID);
        $myrole->deletePermission($this->get('IdPermission'));
    }

    /**
     * Cleans the class errors
     */
    public function clearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    /**
     * Loads the class error
     * 
     * @param int $code
     */
    public function setError(int $code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    /**
     * Returns true if the class had an error
     * 
     * @return boolean
     */
    public function hasError() : bool
    {
        return ($this->numErr != null);
    }
}
