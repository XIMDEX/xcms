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



require_once(XIMDEX_ROOT_PATH . '/inc/repository/NodeEntity.class.php');

class NodeEntity_Link extends NodeEntity {

	/**
	 * Constructor. Acepta un idNode o un Path como parametro a partir del
	 * cual se obtendra el resto de informacion del nodo.
	 * 
	 * @param mixed node IdNode o Path absoluto en ximDEX
	 */
    function NodeEntity_Link($node=null) {
    	
    	parent::NodeEntity($node);
    	
    	if (!$this->get('exists')) {
	    	// Si no existe indico que es un enlace -> Se esta creando...
    		$this->set('islink', true);
    	} else if ($this->get('exists') && !$this->get('islink')) {
    		// Si el nodo existe y no es un enlace se limpia la estructura
    		$this->clear();
    	}
    }
    
    /**
     * Devuelve el contenido de un nodo.
     * 
     * @return string
     */
    function getContent() {
    	
		$df = new DataFactory($this->get('idnode'));
		$content = $df->GetContent();
		return $content;
    }
    
    /**
     * Establece el contenido de un nodo.
     * 
     * @return string
     */
    function setContent($content) {
    	
    	// TODO: No usar DataFactory
    	
//		$df = new DataFactory($this->get('idnode'));
//		$content = is_null($content) ? $entity->getContent() : $content;
//		$ret = $df->SetContent($content);		
//		return $ret;
    }


	/**
	 * Devuelve el descriptor de un nodo
	 * 
	 * @return string
	 */    
    function getDescriptor() {
    	
		// Si el descriptor esta cacheado se devuelve directamente,
    	// el proceso de calcular el descriptor puede ser costoso.
//    	$descriptor = $this->get('descriptor');
//    	if ((boolean)$descriptor) return $descriptor;
//    	
//		$df = new DataFactory($this->get('idnode'));
//		
//		$version = $df->getLastVersion();
//		$subversion = $df->getLastSubversion($version);
//		$tmpfile = $df->GetTmpFile($version, $subversion);
//		
//		if (!empty($tmpfile)) {
//			$descriptor = XIMDEX_ROOT_PATH . Config::getValue('FileRoot') . DIRECTORY_SEPARATOR . $df->GetTmpFile($version, $subversion);
//		} else {
////			$descriptor = tempnam('/tmp', 'xvfs_');
////			if (is_resource($fp = fopen($descriptor, 'w', false))) {
////				fwrite($fp, $this->getContent());
////				fclose($fp);
////			}
//		}
//		
//		// Se cachea el descriptor obtenido
//		$descriptor = is_null($descriptor) ? false : $descriptor;
//		$this->set('descriptor', $descriptor);
//    	return $descriptor;
    }
    
    /**
     * Devuelve el tipo mime del nodo
     * 
     * @return string
     */
    function getMIME() {
    	
//    	$mime = $this->get('mimetype');
//    	if ((boolean)$mime) return $mime;
			
		$descriptor = $this->getDescriptor();
		if (false !== $descriptor) {
		
			$mime = exec(trim('file -bi ' . escapeshellarg($descriptor)));
			
			// Los clientes webDAV no entienden la cadena que indica la codificacion de caracteres,
			// se devuelve unicamente el tipo MIME.
			$mime = explode(';', $mime);
			$mime = $mime[0];
			
		} else {
			$mime = 'Unknown';
		}
    	
    	$this->set('mimetype' ,$mime);
    	return $mime;
    }
    
}

?>