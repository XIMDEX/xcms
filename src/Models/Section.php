<?php

namespace Ximdex\Models;

use Ximdex\Data\GenericData;

class Section extends GenericData
{
    public $_idField = 'IdNode';
    public $_table = 'Section';
    public $_metaData = array(
        'IdNode' => array('type' => "int(12)", 'not_null' => 'true', 'primary_key' => true),
        'idSectionType' => array('type' => 'int(11)', 'not_null' => 'true')
    );
    protected $IdNode;
    protected $idSectionType;
    
    public function getIdNode() : int
    {
        return $this->get('IdNode');
    }
    
    public function setIdNode(int $id) : void
    {
        $this->set('IdNode', $id);
    }
    
    public function getIdSectionType() : int
    {
        return $this->get('idSectionType');
    }
    
    public function setIdSectionType(int $id) : void
    {
        $this->set('idSectionType', $id);
    }
}