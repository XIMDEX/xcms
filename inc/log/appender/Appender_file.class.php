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




// TODO: Try to keep the file open while the object is alive.
// TODO: Profile it.

/**
 *
 */
class Appender_file extends Appender {

	var $_fp;
	var $_file;
	var $_append;

	/**
	 * @param object params['layout']
	 * @param string params['file']
	 * @param append params['append']
	 */
	function Appender_file($params) {

		parent::Appender($params);

		$this->_file = $params['file'];
		$this->_append = $params['append'];

		$this->open($this->_file);
	}

	function open($file=null) {

		// Se comprueba si el fichero tiene permisos de escritura, en caso de que aun no exista
		// el fichero, se comprueba que su directorio tenga permisos de escritura.
		// Si no es posible escribir en el fichero -> die!
		// 15.10.2007 - Se deriva el mensaje al log de PHP en lugar de llamar a die()
		if (is_null($file)) $file = $this->_file;
		$pathInfo = pathinfo( $file );
		$pathInfo['path'] = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'];
		$file = $pathInfo['path'];

		$ret = false;

		if( !is_file($file) ) $file = $pathInfo['dirname'];

		if( is_writable( $file ) ) {
			$mode = $this->_append ? 'a' : 'w';
			$this->_fp = fopen($pathInfo['path'], $mode);
			$ret = is_resource($this->_fp);
		} else {

			$msg = sprintf("Appender_file::open(), No es posible escribir en el fichero %s. %s, %s\n", $pathInfo['basename'], __FILE__, __LINE__ );
			error_log($msg);
			$ret = false;
			//die( $msg );
		}

		return $ret;

	}


	function write(&$event) {

		// Automatically call layout and transform. (Transformated msg in $this->_msg)
		parent::write($event);

		$ret = true;
		if (!is_resource($this->_fp)) $ret = $this->open();

		// En caso de que no se pueda obtener el puntero al fichero de log se deriva
		// el mensaje al log de PHP
		if ($ret) {
			fwrite($this->_fp, $this->_msg."\n");
		} else {
			// Se escribe el error en el log de PHP
			error_log($this->_msg);
		}
	}

	function close() {

		if (is_resource($this->_fp)) fclose($this->_fp);
	}
}

?>
