<?php

/* * ****************************************************************************
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
 * **************************************************************************** */

if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../"));
}

require_once("repository/FileSystemRepository.class.php");
require_once("repository/SolrRepository.class.php");

class DataFactory{

    var $nodeID;     // Identificador del tipo de nodo actual.
    var $numErr;    // Codigo de error.
    var $msgErr;    // Mensaje de Error.
    var $errorList = array(// Lista de errores de la clase.
        1 => 'No existe el Nodo',
        2 => 'Error de conexion con la base de datos',
        3 => 'No se encontro el contenido para la version solicitada',
        4 => 'Error accediendo al sistema de archivos',
        5 => 'Error al establecer el contenido del documento',
        6 => 'Ha ocurrido un error al intentar guardar el documento'
    );
    var $conector;
    private $repository;

    /**
     * Constructor de la clase
     *
     * @name		DataFactory
     * @version	1.0
     * @param	int $nodeID=null (opcional) Identificador del Nodo cargado en el objeto
     * @return	$this
     *
     */
    public function DataFactory($nodeID = null)
    {
        $this->nodeID = (int) $nodeID;
        $rep=Config::GetValue("ActiveRepository");

        switch ($rep) {
            case "solr":
                $options = array
                    (
                    'hostname' => !empty(Config::GetValue("SolrServer"))? Config::GetValue("SolrServer") : "localhost",
                    'port' => !empty(Config::GetValue("SolrPort")) ? Config::GetValue("SolrPort") : 8983,
                    'path' => !empty(Config::GetValue("SolrCorePath")) ? Config::GetValue("SolrCorePath") : ""
                );
                
                $this->repository = new SolrRepository($nodeID, $options);
                break;

            default:
                $this->repository = new FileSystemRepository($nodeID);
        }

        $this->ClearError();
    }

    /**
     *
     * Devuelve el identificador del Nodo cargado en el objeto
     *
     * @name		GetID
     * @author 	Jose I. Villar
     * @version	1.0
     * @return	$nodeID
     *
     */
    public function GetID()
    {
        return $this->repository->GetID();
    }

    /**
     *
     * Devuelve la lista de versiones distintas para el nodo cargado en el objeto
     *
     * @name		GetVersionList
     * @author 	Jose I. Villar
     * @version	1.0
     * @return	array $versions
     *
     */
    public function GetVersionList($order = 'asc')
    {
        return $this->repository->GetVersionList($order);
    }

    /**
     *
     * Devuelve la lista de subversiones de una version concreta para el nodo cargado en el objeto
     *
     * @name		GetSubVersionList
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $version
     * @return	array $versions
     *
     */
    public function GetSubVersionList($version)
    {
        return $this->repository->GetSubVersionList($version);
    }

    /**
     *
     * Devuelve la ultima version del nodo que hay en el objeto
     *
     * @name		GetVersionList
     * @author 	Jose I. Villar
     * @version	1.0
     * @return	int $version
     *
     */
    public function GetLastVersion()
    {
        return $this->repository->GetLastVersion();
    }

    /**
     *
     * Devuelve la ultima SubVersion del la version dada
     *
     * @name		GetSubVersionList
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $version
     * @return	array $subVersions
     *
     */
    public function GetLastSubVersion($version)
    {
        return $this->repository->GetLastSubVersion($version);
    }

    public function getVersionId($version, $subversion)
    {
        return $this->repository->getVersionId($version, $subversion);
    }

    /**
     *
     * Devuelve si ya hay almacenada alguna version del nodo que hay en el objeto
     *
     * @name		HasPreviousVersions
     * @author 	Jose I. Villar
     * @version	1.0
     * @return	int $version
     *
     */
    public function HasPreviousVersions()
    {
        return $this->repository->HasPreviousVersions();
    }

    /**
     *
     * Devuelve el contenido de una version.
     *
     * @name		GetContent
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $versionID = null	: Si se omite este parametro y el siguiente, se tomara la ultima version del nodo en el objeto
     * @param	int $subVersion = null	: Si se omite este parametro, el anterior es el identificador unico de version, en otro caso, es el numero de Version
     * @return	string $content
     *
     */
    public function GetContent($versionID = null, $subVersion = null)
    {
        return $this->repository->GetContent();
    }

    private function _generateCaches($idVersion)
    {
        $this->repository->_generateCaches($idVersion);
    }

    /**
     *
     * Cambia el contenido de una version.
     *
     * @name		SetContent
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	string $content
     * @param	int $versionID = null	: Si se omite este parametro y el siguiente, se tomara la ultima version del nodo en el objeto
     * @param	int $subVersion = null	: Si se omite este parametro, el anterior es el identificador unico de version, en otro caso, es el numero de Version
     *
     */
    public function SetContent($content, $versionID = NULL, $subVersion = NULL, $commitNode = NULL, $type = NULL)
    {
        return $this->repository->SetContent($content, $versionID, $subVersion, $commitNode, $type);
    }

    /**
     *
     * Crea una nueva Version
     *
     * @name		AddVersion
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	bool $newVersion = null
     * @param	string $comment = null
     *
     */
    public function AddVersion($jumpNewVersion = NULL, $comment = NULL, $content = NULL, $commitNode = NULL, $mimetype = NULL)
    {
        return $this->repository->AddVersion($jumpNewVersion, $comment, $content, $commitNode, $mimetype);
    }

    private function _getUniqueFileName()
    {

        return FsUtils::getUniqueFile(Config::getValue("AppRoot") . Config::getValue("FileRoot"));
    }

    /*
     *
     * Recupera una antigua Version y la coloca como nueva
     *
     * @name		RecoverVersion
     * @author 	Jose Luis Fernandez
     * @version	1.0
     * @param	int $version not null
     * @param	int $subversion not null
     * @param	string $comment null
     *
     * */

    public function RecoverVersion($version, $subversion, $comment = null, $render = RENDERIZE_NODES)
    {
        return $this->repository->RecoverVersion($version, $subversion, $comment, $render);
    }

    /*
     *
     * Elimina todas las Versiones del Nodo
     *
     * @name		DeleteAllVersions
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $version
     *
     * */

    public function DeleteAllVersions()
    {
        return $this->repository->DeleteAllVersions();
    }

    /*
     *
     * Elimina todas las subVersiones de la Version dada.
     *
     * @name		DeleteVersion
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $version
     *
     * */

    public function DeleteVersion($version)
    {
        return $this->repository->DeleteVersion($version);
    }

    /*
     *
     * Elimina una SubVersion
     *
     * @name		DeleteSubVersion
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version. El parametros versionID significa el campo IdVersion de la tabla versions si la funcion recibe $subVersions==null. El parametro versionID siginifica el campo Version de la tabla Versions si la funcion recibe $subVersions!=null;
     * @param	int $subVersion = null
     *
     * */

    public function DeleteSubversion($versionID, $subVersion = null)
    {
        return $this->repository->DeleteSubversion($versionID, $subVersion);
    }

    /*
     *
     * Elimina las subVersiones de una determinada version que ya nos sirven. Tiene dos casos de uso, dependiendo del parametro all.
     * 1) $all = false => Elimina todas las subVersiones de la Version dada menos la primera, las X ultimas segun la tabla de configuracion (X >= 1).
     * 2) $all = true  =>  Elimina todas las subVersiones de la Version dada menos la primera.
     *
     * @name		PurgeSubVersions
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $version
     *
     * */

    private function _purgeSubVersions($version, $all = null)
    {
        return $this->repository->_purgeSubVersions($version, $all);
    }

    private function _purgeVersions()
    {
        return $this->repository->_purgeVersions();
    }

    /*
     *
     * Devuelve el nombre del archivo temporal en el que se guarda el contenido de una SubVersion
     *
     * @name		GetTmpFile
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param	int $subVersion = null
     *
     * */

    public function GetTmpFile($versionID, $subVersion = null)
    {
        return $this->repository->GetTmpFile($versionID, $subVersion);
    }

    /*
     *
     * Devuelve el comentario de una SubVersion
     *
     * @name		GetComment
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param	int $subVersion = null
     *
     * */

    public function GetComment($versionID, $subVersion = null)
    {
        return $this->repository->GetComment($versionID, $subVersion);
    }

    /**
     *
     * Devuelve el la fecha de almacenamiento de una SubVersion
     *
     * @name		GetDate
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param	int $subVersion = null
     *
     * */
    public function GetDate($versionID, $subVersion = null)
    {
        return $this->repository->GetDate($versionID, $subVersion);
    }

    /*
     *
     * Devuelve el id del usuario que gestiono la version.
     *
     * @name		GetUserID
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $versionID	: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param	int $subVersion = null
     *
     * */

    public function GetUserID($versionID, $subVersion = null)
    {
        return $this->repository->GetUserID($versionID, $subVersion);
    }

    // Se queda de wrapper
    public function GetVersionAndSubVersion($versionID)
    {
        return $this->repository->GetVersionAndSubVersion($versionID);
    }

    /*
      Solo se usa en el script devel/scripts/ficheros_sobrantesIO.php
     */

    public function GetFiles()
    {
        return $this->repository->GetFiles();
    }

    /*
     *
     * Resetea el flag de ocurrencia de error
     *
     * @name		ClearError
     * @author 	Jose I. Villar
     * @version	1.0
     *
     * */

    public function ClearError()
    {
        $this->repository->ClearError();
    }

    /*
     *
     * Carga en el objeto el codigo del ultimo error ocurrido
     *
     * @name		SetError
     * @author 	Jose I. Villar
     * @version	1.0
     * @param	int $code
     *
     * */

    public function SetError($code)
    {
        $this->repository->SetError($code);
    }

    /*
     *
     * Devuelve un booleano que indica si hubo algun error
     *
     * @name		HasError
     * @author 	Jose I. Villar
     * @version	1.0
     * @return	bool $hasError
     *
     * */

    public function HasError()
    {
        return $this->repository->HasError();
    }

    /*
      Devuelve el idversion correspondiente a la version publicada actualmente
     */

    public function GetPublishedIdVersion()
    {
        return $this->repository->GetPublishedIdVersion();
    }

    /*
      Si la version de la noticia en el colector la publicada devuelve false
     */

    public function isEditedForPublishing($versionInColector)
    {
        return $this->repository->isEditedForPublishing($versionInColector);
    }

    public function GetLastVersionId()
    {
        return $this->repository->GetLastVersionId();
    }

    public function GetVersionFromId($idVersion)
    {
        return $this->repository->GetVersionFromId($idVersion);
    }

    public function GetPreviousVersion($idVersion)
    {
        return $this->repository->GetPreviousVersion($idVersion);
    }

    public function indexNode($idVersion, $commitNode)
    {
        return $this->repository->indexNode($idVersion, $commitNode);
    }

}

?>
