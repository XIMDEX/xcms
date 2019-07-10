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

class RelTemplateContainer extends GenericData
{
    public $_idField = 'IdRel';
    
    public $_table = 'RelTemplateContainer';
    
    public $_metaData = array
    (
        'IdRel' => array('type' => "int(12)", 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdTemplate' => array('type' => "int(12)", 'not_null' => 'true'),
        'IdContainer' => array('type' => "int(12)", 'not_null' => 'true')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('IdRel');
    
    public $IdRel;
    
    public $IdTemplate = 0;
    
    public $IdContainer = 0;

	function getTemplate(int $idContainer)
	{
		$template = $this->find('IdTemplate', 'IdContainer = %s', array($idContainer), MULTI);
		if (! empty($template)) {
			$last = end($template);
			return $last['IdTemplate'];
		} else {
			return NULL;
		}
	}

	public function createRel(int $idTemplate, int $idNode) : ?int
	{
		$this->set('IdRel', NULL);
		$this->set('IdTemplate', $idTemplate);
		$this->set('IdContainer', $idNode);
		if (parent::add()) {
			$idRel = (int) $this->get('IdRel');
		} else {
			return null;
		}
		$container = new Node($idNode);
		$arr_child = $container->getChildren();
		if (! is_null($arr_child)) {
			foreach ($arr_child as $child) {
				$doc = new StructuredDocument($child);
				$version = $doc->getLastVersion();
				$dependencies = new Dependencies();
				$dependencies->insertDependence($idTemplate, $child, 'PVD', $version);
			}
		}
		return $idRel;
	}

    public function deleteRel(int $idContainer) : bool
    {
		$db = new \Ximdex\Runtime\Db();
        $sql = "DELETE FROM RelTemplateContainer Where IdContainer = $idContainer";
        return $db->execute($sql);
    }

	public function deleteRelByTemplate(int $idTemplate) : bool
	{
		$db = new \Ximdex\Runtime\Db();
		$sql = "DELETE FROM RelTemplateContainer WHERE IdTemplate = $idTemplate";
        return $db->execute($sql);
	}
}
