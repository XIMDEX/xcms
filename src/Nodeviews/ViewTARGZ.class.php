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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\Nodeviews;

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Models\StructuredDocument;
use Ximdex\Models\Version;
use Ximdex\Runtime\App;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\TarArchiver;

class ViewTARGZ extends AbstractView
{
    public function transform(int $idVersion = null, string $pointer = null, array $args = null)
    {
        $content = $this->retrieveContent($pointer);
        
        // Validating data
        $version = new Version($idVersion);
        if (! $version->get('IdVersion')) {
            Logger::error("Se ha cargado una versión incorrecta ($idVersion)");
            return null;
        }
        $node = new Node($version->get('IdNode'));
        $nodeId = $node->get('IdNode');
        if (! $nodeId) {
            Logger::error('El nodo que se está intentando convertir no existe: ' . $version->get('IdNode'));
            return null;
        }
        if (! array_key_exists('PATH', $args) && ! array_key_exists('NODENAME', $args)) {
            Logger::error('Path and nodename arguments are mandatory');
            return null;
        }
        $tarFile = $args['PATH'];
        $tmpFolder = XIMDEX_ROOT_PATH . App::getValue('TempRoot');

        // Sets content on SQL and XML files
        $arrayContent = explode('<sql_content>', $content);
        $tmpDocFile = $tmpFolder . '/' . $args['NODENAME'] . '.xml';
        $tmpSqlFile = $tmpFolder . '/' . $args['NODENAME'] . '.sql';
        $xmlContent = $arrayContent[0];
        $sqlContent = substr(trim($arrayContent[1]), 0, -14);

        // Encode the content to ISO, now OTF only work in ISO mode, because the jsp files are in ISO too
        $xmlContent = \Ximdex\XML\Base::recodeSrc($xmlContent, \Ximdex\XML\XML::ISO88591);
        if (! FsUtils::file_put_contents($tmpDocFile, $xmlContent)) {
            return false;
        }
        $sqlContent = \Ximdex\XML\Base::recodeSrc($sqlContent, \Ximdex\XML\XML::ISO88591);
        if (! FsUtils::file_put_contents($tmpSqlFile, $sqlContent)) {
            return false;
        }

        // Making tar file with the aditional files
        $tarArchiver = new TarArchiver($tarFile);
        $tarArchiver->addEntity($tmpDocFile);
        $tarArchiver->addEntity($tmpSqlFile);

        // Removing tar extension
        rename($tarFile . '.tar', $tarFile);
        return $this->storeTmpContent($arrayContent[0]);
    }

    /**
     * Return the file name about the params
     *
     * @param $tableName
     * @param $field
     * @param $condition
     * @param $params
     * @return String filename
     */
    function getFile($tableName, $field, $condition, $params)
    {
        $factory = new \Ximdex\Utils\Factory(XIMDEX_ROOT_PATH . '/src/Models/', $tableName);
        $object = $factory->instantiate(null, null, '\Ximdex\Models');
        if (! is_object($object)) {
            Logger::info("Error, la clase $tableName de orm especificada no existe");
            return null;
        }
        $result = $object->find($field, $condition, $params, MULTI);
        if (! is_null($result)) {
            reset($result);
            $fileName = $result[0][0];
            return $fileName;
        }
        Logger::info("Additional file for $tableName not found");
        return null;
    }

    /**
     * Generate the bulletin docxap for OTF
     *
     * @param Node $node
     * @param Int $idVersion
     * @return String
     */
    function generateDocXapForBulletin($node, $idVersion)
    {
        // Docxap for return it
        $docxapout = '';
        $channels = '';
        $language = '';

        // Check that the Version is ok
        if (! is_null($idVersion)) {
            $version = new Version($idVersion);
            if (! $version->get('IdVersion')) {
                Logger::error('VIEW TARGZ: Se ha cargado una versión incorrecta (' . $idVersion . ')');
                return '';
            }
            $structuredDocument = new StructuredDocument($version->get('IdNode'));
            $channels = $structuredDocument->GetChannels();
            $language = $structuredDocument->GetLanguage();
            if (! $structuredDocument->get('IdDoc')) {
                Logger::error('VIEW TARGZ: El structured document especificado no existe: ' . $structuredDocument->get('IdDoc'));
                return '';
            }
            
            // If it is all ok
            if ((is_array($channels)) && (! is_null($node)) && (! is_null($structuredDocument)) && (array_key_exists(0, $channels)) 
                && (! is_null($language))) {
                
                // Select, for example, the first channel, it's the same because otf will renderize the
                // xml with a channel selected by the user
                $channel = reset($channels);
                $documentType = $structuredDocument->GetDocumentType();
                $docxapout = $node->class->getDocHeader($channel, $language, $documentType);

                // Check out
                if (! isset($docxapout) || $docxapout == '') {
                    Logger::error('VIEW TARGZ: No se ha especificado la cabecera docxap del nodo ' . $node->GetNodeName() 
                        . ' que quiere renderizar');
                    return '';
                }
            } else {
                Logger::error('VIEW TARGZ:No se ha podido generar la etiqueta doxcap para el boletin, renderizado para OTF');
            }
            return $docxapout;
        } else {
            Logger::error('VIEW TARGZ:No se ha podido generar la etiqueta doxcap para el boletin, renderizado para OTF');
        }
    }

    private function getLastVersion($idNode)
    {
        $sql = "select IdVersion from Versions where IdNode = $idNode order by Version desc limit 1;";
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($sql);
        while (! $dbObj->EOF) {
            $idVersion = $dbObj->GetValue('IdVersion');
            $dbObj->Next();
        }
        return $idVersion;
    }
}
