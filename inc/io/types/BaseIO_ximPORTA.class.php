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

ModulesManager::file('/actions/fileupload_document_multiple/baseIO.php', 'ximPORTA');

class BaseIO_ximPORTA
{
    public $messages;

    public function BaseIO_ximPORTA()
    {
    }

    public function build($data)
    {
        $uploader = new FileUploadDocumentMultiple();

        // languages array. Each language has an array with the channels ids.
        $languageData = array ($data['LANG'] => array('channels' => $data['CHANNELS']));

        // array de archivos => un subarray por cada elemento con las claves tmp_name(ruta) y name(nombre del nodo)
        $files = array(
                array('tmp_name' => $data['PATH'], 'name' => $data['NAME'])
            );
        $result = $uploader->insertDocuments($data['PARENTID'], $data['TEMPLATE'], $languageData, $files, false);
        $this->messages = $uploader->messages;
        if (count($uploader->insertedIds) == 1) {
            return $uploader->insertedIds[0];
        } elseif (count($uploader->insertedIds) > 1) {
            XMD_Log::error('Ha ocurrido un error inesperado al importar un nodo ximPORTA, se han encontrado dos inserciones cuando se esperaba una');
        }

        return NULL;
    }
}
