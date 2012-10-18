<?php
/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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



class unique extends BehaviorBase {

	/* Required */
	var $required = array('field');
	
	function beforeAdd(&$model) {
		return $this->checkField($model);
	}

	function beforeUpdate(&$model) {
		return $this->checkField($model, $model->get($model->_idField));
	}
	
	private function checkField(&$model, $id = null) {
		$fieldValue = $model->get($this->options['field']);
		if (empty($id)) {
			$result = $model->find(ALL, sprintf('%s = %%s', $this->options['field']), 
				array($fieldValue));
		} else {
			$result = $model->find(ALL, sprintf('%s = %%s AND %s <> %%s', $this->options['field'], $model->_idField), 
				array($fieldValue, $model->get($model->_idField)));
		}

		if (!empty($result)) {
			$this->messages->add(sprintf(_('El campo %s debe ser único'), 
				$this->options['field']), MSG_TYPE_ERROR);
			return false;
		}
		return true;
	}
}

?>
