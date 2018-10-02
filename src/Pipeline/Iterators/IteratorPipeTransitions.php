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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace  Ximdex\Pipeline\Iterators;

use Ximdex\Behaviours\Iterator;

/** 
 * @brief Basic iterator for the PipeTransition class
 * 
 * Basic iterator for the PipeTransition class, this iterator behaves sorting 
 * the results in the sequence of the status stored in the database
 */
class IteratorPipeTransitions extends Iterator
{
	var $_objectName = '\\Ximdex\\Models\\PipeTransition';
	
	/**
	 * Carga el iterador de la condicion dada y lo ordena por sus transiciones
	 *
	 * @param string $condition
	 * @param array $args
	 * @return \Ximdex\Pipeline\Iterators\IteratorPipeTransitions
	 */
	public function __construct($condition, $args)
	{
		parent::__construct($condition, $args);
		$this->_initialize();
		$this->_sort();
	}
	
	private function _sort()
	{
		$totalElements = $this->count();
		for ($key = 0; $key < $totalElements; $key++) {
			do {
				$value = $this->_objects[$key]->get('IdStatusFrom');
			} while($this->_swap($value, $key));
		}
	}
	
	private function _swap($value, $key)
	{
		$this->seek($key + 1);
		while($element = $this->next()) {
			if ($element->get('IdStatusTo') == $value) {
			    
				// Swap elements
				$tmp = $this->_objects[$key];
				$this->_objects[$key] = $element;
				$this->_objects[$this->key() -1] = $tmp;
				
				// End elemetns swaping
				$this->reset();
				return true;
			}
		}
		$this->reset();
		return false;
	}
	
	private function _initialize() {
		$this->reset();
		$index = 0;
		if (!($this->count()) > 0) {
			return;
		}
		$statusFromArray = [];
		$statusToArray = [];
		while ($element = $this->next()) {
			$statusFromArray[$index] = $element->get('IdStatusFrom'); 
			$statusToArray[] = $element->get('IdStatusTo'); 
			$index ++;
		}
		foreach ($statusFromArray as $key => $idStatusFrom) {
			if (in_array($idStatusFrom, $statusToArray)) {
				continue; 
			}
			if ($key === 0) {
				break;
			}
			$tmpElement = $this->_objects[$key];
			$this->_objects[$key] = $this->_objects[0];
			$this->_objects[0] = $tmpElement;
			break;
		}
	}
}
