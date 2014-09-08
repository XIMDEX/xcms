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



 
 
require_once( XIMDEX_ROOT_PATH . '/inc/log/appender/Appender_file.class.php' );
require_once( XIMDEX_ROOT_PATH . '/inc/fsutils/TarArchiver.class.php' );
require_once( XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php' );

class Appender_rollingfile extends Appender_file {
	
	var $_maxFileSize;
	var $_compress;
	
	/**
	 * @param object params['layout'] Formato de salida
	 * @param string params['file'] Fichero de registro
	 * @param string params['maxsize'] Tamanno maximo del fichero de registro antes de que sea rotado
	 * @param boolean params['compress'] Indica si se debe comprimir el fichero de registro rotado
	 */
	function Appender_rollingfile( &$params ) {
		parent::Appender_file( $params );
		$this->setFile( $params['file'] );
		if( array_key_exists("maxsize", $params) && !empty($params["maxsize"]) ) {
		  $this->setMaxFileSize( $params['maxsize'] );
		}else { //Default: 50MB
		  $this->setMaxFileSize( "250MB" );
		}

		if( array_key_exists("compress", $params) && null != $params["compress"] ) {
		  $this->setCompress($params["compress"] );
		}else {  //Default:true
		  $this->setCompress( true );
		}
	}
	
	/**
	 * Establece el fichero en el que se volcaran los registros
	 * @param string file
	 */
	function setFile( $file ) {
		if( is_resource($this->_fp) ) $this->close();
		$this->_file = $file;
		$this->open( $this->_file );
	}
	
	/**
	 * Establece el tamanno maximo que ocupara el fichero antes de ser rotado.
	 * Debe ser una cadena que exprese la unidad de medida [KB|MB|GB], si se omite la unidad se usara KB.
	 * @param string size Tamanno maximo del fichero de registro antes de que sea rotado
	 */
	function setMaxFileSize( $size ) {
	
		$regexp = '#(\d+)(KB|MB|GB)?#i';
		$exp = array();
		preg_match_all( $regexp, $size, $exp, PREG_SET_ORDER );
		
		$size = (int) sprintf( '%u', $exp[0][1] );
		if( !isset($exp[0][2]) ) $exp[0][2] = 'KB';
		
		switch( strtoupper( $exp[0][2] ) ) {
			case 'KB':
				$size *= 1024;
				break;
			case 'MB':
				$size *= pow(1024, 2);
				break;
			case 'GB':
				$size *= pow(1024, 3);
				break;			
		}
		
		// No es recomendable un tamanno mayor de 2GB
		$twogb = 2 * pow(1024, 3);
		if( $size > $twogb ) $size = $twogb;		
		$this->_maxFileSize = $size;
		
	}
	
	/**
	 * Establece si se comprimira el fichero rotado
	 * @param boolean compress Indica si el fichero sera comprimido
	 */
	function setCompress( $compress ) {
		$this->_compress = $compress;
	}
	
	function write( &$event ) {
		parent::write( $event );
		$this->rollOver();
	}
	
	/**
	 * Se encarga de rotar el fichero de registro en funcion de su tamanno
	 */
	function rollOver() {
		
		if( !file_exists( $this->_file) ) return;
		
		// PHP guarda informacion de los ficheros en cache al usar varias funciones, filesize() entre otras...
		// Limpiamos la cache para poder obtener el tamanno correcto.
		clearstatcache();
		$fileSize = (int) sprintf( '%u', filesize( $this->_file ) );
		if( $fileSize < $this->_maxFileSize ) return;

		$c = 0;
		$newFile = $this->_file;
		$newFileCompressed = $this->_file;
		$pathInfo = pathinfo( $this->_file );
		
		// Obtiene un nombre de fichero que no exista aun
		while( is_file( $newFile ) || is_file( $newFile.'.tar.gz' ) ) {
			
			$c++;
			$newFile = sprintf( '%s%s%s.%s.%s', $pathInfo['dirname'], DIRECTORY_SEPARATOR, $pathInfo['basename'], $c, $pathInfo['extension'] );
			$newFile = str_replace( "{$pathInfo['extension']}.$c", $c, $newFile );
			
		}
		
		$this->close();
		rename( $this->_file, $newFile );
		$this->open( $this->_file );
		
		
		if( $this->_compress ) {
			
			/**
			 * La forma directa .....
			 * 
			$cmd = sprintf( 'tar --gzip --create --directory=%s --file=%s.tar.gz %s', $pathInfo['dirname'], $newFile, $patInfo['basename'] );
			exec( $cmd );
			 */
			
			$fileInfo = pathinfo( $newFile );
			$tmpDir = Config::getValue('AppRoot') . Config::getValue('TempRoot') . DIRECTORY_SEPARATOR . 'xmdlogs';
			$tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $fileInfo['basename'];
			
			$tar = new TarArchiver( $newFile, array( TAR_COMPRESSION=>TAR_COMPRESSION_GZIP ) );
			$tar->addEntity( $newFile );
			$tar->pack( $tmpDir );
			
			// TarArchive no elimina los ficheros temporales
			FsUtils::delete($tmpFile);
			FsUtils::delete($newFile);
			
		}
		
	}
	
}
 
?>