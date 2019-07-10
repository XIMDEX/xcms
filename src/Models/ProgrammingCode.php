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

namespace Ximdex\Models;

use Ximdex\Data\GenericData;

class ProgrammingCode extends GenericData
{
    public $_idField = 'id';
    
    public $_table = 'ProgrammingCode';
    
    public $_metaData = array(
        'id' => array('type' => "int(4)", 'not_null' => 'true', 'primary_key' => true),
        'idLanguage' => array('type' => 'varchar(20)', 'not_null' => 'true'),
        'idCommand' => array('type' => 'varchar(20)', 'not_null' => 'true'),
        'code' => array('type' => 'text', 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array('idProgLanguage' => array('idLanguage', 'idCommand'));
    
    protected $id;
    
    protected $idLanguage;
    
    protected $idCommand;
    
    protected $code;
    
    /**
     * Load the code for the current programming language and command
     * 
     * @param array $params
     * @return bool
     */
    public function translate(array $params = []) : bool
    {
        if (! $this->getIdLanguage()) {
            $this->messages->add('The language ID field is empty', MSG_TYPE_WARNING);
            return false;
        }
        if (! $this->getIdCommand()) {
            $this->messages->add('The command ID field is empty', MSG_TYPE_WARNING);
            return false;
        }
        $res = $this->find('*', 'idLanguage = \'' . $this->getIdLanguage() . '\' and idCommand = \'' . $this->getIdCommand() . '\'');
        if (! $res or ! isset($res[0])) {
            $this->messages->add('Cannot obtain translated code for the language ' . $this->getIdLanguage() . ' and command ' 
                . $this->getIdCommand(), MSG_TYPE_ERROR);
            return false;
        }
        $res = $res[0];
        if (! $res or ! isset($res['code']) or ! $res['code']) {
            $this->messages->add('There is not a code for language and command given', MSG_TYPE_ERROR);
            return false;
        }
        if ($params) {
            $res['code'] = vsprintf($res['code'], $params);
        }
        $this->setCode($res['code']);
        return true;
    }
    
	public function getId() : int
	{
		return $this->get('id');
	}
    
	public function setId(int $id) : void
	{
	   $this->set('id', $id);
	}
	
	public function getIdLanguage() : string
	{
	    return $this->get('idLanguage');
	}
	
	public function setIdLanguage(string $idLanguage) : void
	{
	    $this->set('idLanguage', $idLanguage);
	}
	
	public function getIdCommand() : string
	{
	    return $this->get('idCommand');
	}
	
	public function setIdCommand(string $idCommand) : void
	{
	    $this->set('idCommand', $idCommand);
	}
	
	public function getCode() : string
	{
	    return $this->get('code');
	}
	
	public function setCode(string $code) : void
	{
	    $this->set('code', $code);
	}
}
