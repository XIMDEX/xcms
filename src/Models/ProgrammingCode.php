<?php

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
        if (!$this->getIdLanguage())
        {
            $this->messages->add('The language ID field is empty', MSG_TYPE_WARNING);
            return false;
        }
        if (!$this->getIdCommand())
        {
            $this->messages->add('The command ID field is empty', MSG_TYPE_WARNING);
            return false;
        }
        $res = $this->find('*', 'idLanguage = \'' . $this->getIdLanguage() . '\' and idCommand = \'' . $this->getIdCommand() . '\'');
        if (!$res or !isset($res[0]))
        {
            $this->messages->add('Cannot obtain translated code for the language ' . $this->getIdLanguage() . ' and command ' 
                . $this->getIdCommand(), MSG_TYPE_ERROR);
            return false;
        }
        $res = $res[0];
        if (!$res or !isset($res['code']) or !$res['code'])
        {
            $this->messages->add('There is not a code for language and command given', MSG_TYPE_ERROR);
            return false;
        }
        if ($params)
        {
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