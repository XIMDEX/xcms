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



require_once(XIMDEX_ROOT_PATH . '/inc/fsutils/FsUtils.class.php');


abstract class Abstract_View {
	const TMP_FOLDER = '/data/tmp/';
	
	public function storeTmpContent($content) {
		//Si el contenido es una variable que contiene false ha ocurrido un error
		if ($content !== false) {
			$basePath = XIMDEX_ROOT_PATH . self::TMP_FOLDER; 
			$pointer = FsUtils::getUniqueFile($basePath);
			if (FsUtils::file_put_contents($basePath . $pointer, $content)) {
				return $basePath . $pointer;
			}
		}
		XMD_Log::error('Ha sucedido un error al intentar almacenar contenido');
		return NULL;
	}
	
	public function retrieveContent($pointer) {
		return FsUtils::file_get_contents($pointer);
	}
}

?>