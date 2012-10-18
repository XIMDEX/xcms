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




/**
*  @brief Abstract class for build the XVFS_Entity classes.
*/

abstract class XVFS_Entity {

	/**
	 * @var array Estructura que guarda la informacion de la entidad
	 */
	var $_data;
	
	
	/**
	 * Constructor. Necesita el path en el backend
	 * 
	 * @param string bpath Backend path
	 */
	function XVFS_Entity($bpath) {
		$this->_data = array();
		$this->_data['bpath'] = $bpath;
	}
	
	/**
	 * Devuelve el valor del atributo indicado.
	 * 
	 * @param string key Nombre del atributo a recuperar.
	 * @return mixed El valor del atributo o NULL si no existe el atributo.
	 */
	function & get($key) {
		$ret = null;
		if (isset($this->_data[$key])) $ret = $this->_data[$key];
		return $ret;
	}
	
	/**
	 * Establece el valor de un atributo.
	 * 
	 * @param string key Nombre del atributo.
	 * @param mixed value Valor que se le asignara.
	 * @return Devuelve el valor establecido.
	 */
	function set($key, $value) {
		$this->_data[$key] = $value;
		return $value;
	}
	
	/**
	 * Devuelve TRUE si la entidad existe
	 * 
	 * @return boolean
	 */
	function exists() {
		return $this->get('exists');
	}
	
	/**
	 * Devuelve el array con la informacion del nodo.
	 * 
	 * @return array
	 */
	function asArray() {
		$ret = $this->_data;
		return $ret;
	}
	
	/**
	 * Limpia toda la informacion del nodo, usada por los descendientes de esta clase
	 */
	function clear() {
		
		$data =& $this->_data;
		
		foreach ($data as $key=>$value) {
			$data[$key] = null;
		}
		
		$data['isdir'] = false;
		$data['isfile'] = false;
		$data['islink'] = false;
		$data['exists'] = false;
	}
	
	/**
	 * Carga la entidad con la informacion contenida en un array
	 * 
	 * @param array data Array de datos
	 */
	function loadData($data) {
		
		if (!is_array($data)) return false;
		
		$this->clear();
		
		foreach ($data as $key=>$value) {
			$this->_data[$key] = $value;
		}
	}
    
	/**
	 * Obtiene el descriptor de la entidad
	 * 
	 * @return string
	 */
	function getDescriptor() {

		return $this->get('descriptor');
	}
    
	/**
	 * Obtiene el tipo MIME de la entidad
	 * 
	 * @return string
	 */
	function getMIME() {

		return $this->get('mimetype');
	}
	
}

?>
