<?php

namespace Ximdex\Models;

use Ximdex\Data\GenericData;

class ProgrammingLanguage extends GenericData
{
    public $_idField = 'id';
    public $_table = 'ProgrammingLanguage';
    public $_metaData = array(
        'id' => array('type' => "varchar(20)", 'not_null' => 'true', 'primary_key' => true),
        'description' => array('type' => 'varchar(50)', 'not_null' => 'true')
    );
    protected $id;
    protected $description;
    
	public function getId() : string
	{
		return $this->get('id');
	}
    
	public function setId(string $id) : void
	{
	   $this->set('id', $id);
	}
	
	public function getDescription() : string
	{
	    return $this->get('description');
	}
	
	public function setDescription(string $description) : void
	{
	    $this->set('description', $description);
	}
}