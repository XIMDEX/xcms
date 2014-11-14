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



require_once(XIMDEX_XVFS_PATH . '/backends/XVFS_Backend_connector.class.php');

/**
 * @brief Backend for access to a ftp repository.
 *
 * Implementation of ximDEX backend.
 *
 * NOTE: Las operaciones que crean entidades no devuelven un error si el recurso
 * indicado ya existe en el repositorio, sino que devuelven un codigo indicando que
 * el metodo se ejecuto correctamente.
 * Esto es un workaround necesario cuando se llama al backend desde webDAV.
 *
 */
class XVFS_Backend_ftp extends XVFS_Backend_connector implements Backend_XVFS_interface {

}

?>