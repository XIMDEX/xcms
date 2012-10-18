<?php

/******************************************************************************
 *  Ximdex a Semantic Content Management System (CMS)    							*
 *  Copyright (C) 2011  Open Ximdex Evolution SL <dev@ximdex.org>	      *
 *                                                                            *
 *  This program is free software: you can redistribute it and/or modify      *
 *  it under the terms of the GNU Affero General Public License as published  *
 *  by the Free Software Foundation, either version 3 of the License, or      *
 *  (at your option) any later version.                                       *
 *                                                                            *
 *  This program is distributed in the hope that it will be useful,           *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of            *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             *
 *  GNU Affero General Public License for more details.                       *
 *                                                                            *
 * See the Affero GNU General Public License for more details.                *
 * You should have received a copy of the Affero GNU General Public License   *
 * version 3 along with Ximdex (see LICENSE).                                 *
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.                       *
 *                                                                            *
 * @version $Revision: $                                                      *  
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

class ImageFile {
	private $idNode;
	private $file;
	
	function __construct($_id_node = -1) {
		$this->ImageFileConstruct($_id_node);
	}
	
	private function ImageFileConstruct($_id_node = 1) {
		if(-1 != $_id_node) {
			$this->idNode = (int) $_id_node;
			$this->getFile();
		}
	}

	
	function getFile($_id_node = -1, $_version = -1, $_subversion = -1) {
		$_id_node = $this->_getNode($_id_node);
	
	   if (is_numeric($_version) && is_numeric($_subVersion) && $_version != -1 && $_subversion != -1 ) {
    		$dataFactory = new DataFactory($_id_node);
    		$selectedVersion = $dataFactory->getVersionId($_version, $_subversion);
    	} else {
	    	$dataFactory = new DataFactory($_id_node);
	    	$selectedVersion = $dataFactory->GetLastVersionId();
    	}
    	
  		$version = new Version($selectedVersion);
    	$hash = $version->get('File');
	
    	if(!empty($hash) ) 
    		$this->file = XIMDEX_ROOT_PATH ."/data/files/".$hash;
    	else
    		$this->file = null;
    	
    	  return $this->file;
	}


	function saveTags($_tags, $_id_node = -1) {
		$this->ImageFileConstruct($_id_node);

		if(null != $this->file) {
			//025 iptc_keyword, see more in http://php.net/manual/en/function.iptcembed.php
			$this->write_exif("025", $_tags); 
		}
	}
	
	function write_exif($_field, $_value) {
		$image = getimagesize($this->file, $info);
//		if(isset($info['APP13'])) {
//			return false;  //Error: IPTC data found in source image, cannot continue
//		}
		

		$utf8seq = chr(0x1b) . chr(0x25) . chr(0x47);
		$length = strlen($utf8seq);
		$data = chr(0x1C) . chr(1) . chr('090') . chr($length >> 8) . chr($length & 0xFF) . $utf8seq;

		//create data for exif   
   	$data .= $this->_iptc_make_tag(2, $_field, $_value);
   	 
   	// Embed the IPTC data
		$content = iptcembed($data, $this->file);
			
			
		$node = new Node($this->idNode);
		$node->SetContent($content);

	}
	
	
	
	// iptc_make_tag() function by Thies C. Arntzen
	private function _iptc_make_tag($rec, $data, $value)
	{
		 $length = strlen($value);
		 $retval = chr(0x1C) . chr($rec) . chr($data);

		 if($length < 0x8000)
		 {
		     $retval .= chr($length >> 8) .  chr($length & 0xFF);
		 }
		 else
		 {
		     $retval .= chr(0x80) . 
		                chr(0x04) . 
		                chr(($length >> 24) & 0xFF) . 
		                chr(($length >> 16) & 0xFF) . 
		                chr(($length >> 8) & 0xFF) . 
		                chr($length & 0xFF);
		 }

		 return $retval . $value;
	}


	
	private function _getNode($_id_node = -1) {
		return ($_id_node != -1)? $_id_node : $this->idNode;
	}
}
?>
