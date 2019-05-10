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

use Ximdex\Data\GenericData;

class SemanticNamespaces extends GenericData
{
    public $_idField = 'idNamespace';
    
    public $_table = 'SemanticNamespaces';
    
    public $_metaData = array(
        'idNamespace' => array('type' => 'int(11)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'service' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'type' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'nemo' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'uri' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'recursive' => array('type' => 'mediumint(8)', 'not_null' => 'true'),
        'category' => array('type' => 'varchar(255)', 'not_null' => 'true'),
        'isSemantic' => array('type' => 'mediumint(1)', 'not_null' => 'true'),
    );
    
    public $_uniqueConstraints = array(
        'Nemo' => array('nemo'),
        'Type' => array('type')
    );
    
    public $_indexes = array('idNamespace');
    
    /**
     * Autoincrement id
     * 
     * @var int
     */
    public $idNamespace;
    
    /**
     * Source which provice the type. P.e Ximdex, DBpedia
     * 
     * @var string
     */
    public $service;
    
    /**
     * Specific type for a tag. P.e. DBPediaPeople
     * 
     * @var string
     */
    public $type;
    
    /**
     * Mnemonic for ximdex document tag. it could be an attribute
     * 
     * @var string
     */
    public $nemo;
    
    /**
     * To source
     * 
     * @var string
     */
    public $uri;
    
    /**
     * If the type has more descendant types
     * 
     * @var integer
     */
    public $recursive = 0;
    
    /**
     * Kind of the source. P.e. Images, Article, Generic
     * 
     * @var string
     */
    public $category;
    
    public $isSemantic = 0;


	/**
	* Get an array with an Namespace object for every namespace row
	* 
	* @return array
	*/
	public function getAll()
	{
		$result = array();
		$namespaces = $this->find('idNamespace');
		if ($namespaces !== null) {
			foreach ($namespaces as $nspace) {
			    $result[] = new SemanticNamespaces($nspace['idNamespace']);
			}
		}
		return $result;
	}

	public function getByUri(string $uri)
	{
		$result = array();
		$namespaces = $this->find('idNamespace', "uri = '$uri'");
		if ($namespaces !== null) {
			foreach ($namespaces as $nspace) {
			    $result[] = new SemanticNamespaces($nspace['idNamespace']);
			}
		}
		return $result;
	}

	public function getByNemo(string $nemo)
	{
    	$result = array();
		$namespaces = $this->find('idNamespace', "nemo = '$nemo'");
		if ($namespaces !== null) {
			foreach ($namespaces as $nspace) {
				$result[] = $nspace['idNamespace'];
			}
		}
		$result = count($result)? $result[0]: false;
		return $result;
	}
	
    public function getNemo(int $idNamespace)
    {
        $res = $this->find('nemo', 'idNamespace = %s', array($idNamespace), MONO);
        return $res[0]; 
    }
}
