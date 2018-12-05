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

namespace Ximdex\Runtime;

use Ximdex\Models\Node;
use Ximdex\Models\PipeCache;
use Ximdex\Models\User;
use Ximdex\Models\Version;
use Ximdex\Utils\FsUtils;
use Ximdex\Utils\PipelineManager;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Logger;
use Ximdex\NodeTypes\NodeTypeConstants;

/**
 * Clase que modela el Repositorio de Versiones a bajo nivel.
 *
 * Gestiona el repositorio central de datos de ximDEX.
 * Para cada nodo, almacena su contenido y lleva un sistema de versionado,
 * configurable desde la tabla Config.
 *
 * El sistema de nombrado de versiones utiliza dos indices: version y
 * subversion. Cuando el coontenido es simplemente modificado, salta a la
 * siguiente subversion y cuendo es publicado se crea una nueva version.
 *
 * Al pasar a una nueva version se procede a limpiar las subversiones de la
 * version anterior, manteniendo siempre la primera subversion (*.0, que es la
 * que se publica.
 *
 * Las dos claves de configuracion son:
 *
 * 1) PurgeSubversionsOnNewVersion - decide si al saltar a una nueva version
 * se debe proceder al limpiado de subversiones de la version anterior.
 * El proceso de limpiado forzosamente respetara la *.0.
 *
 * 2) MaxSubVersionsAllowed - Durante el desarrollo de una version, se procede
 * a guardar en multitud de ocasiones, generando un gran numero de subversiones
 * , por lo que este parametro indica cuantas queremos guardar. Asi, el sistema
 * unicamente guardara la primera subversion, y las N ultimas, donde N viene
 * definido por este parametro.
 */
class DataFactory
{
    /**
     * Identificador del tipo de nodo actual
     * 
     * @var int
     */
    public $ID;

    /**
     * Codigo de error
     * 
     * @var int
     */
    public $numErr;

    /**
     * Mensaje de Error
     * 
     * @var string
     */
    public $msgErr;
    
    /**
     * Lista de errores de la clase
     * 
     * @var array
     */
    public $errorList = array(
        1 => 'No existe el Nodo',
        2 => 'Error de conexion con la base de datos',
        3 => 'No se encontro el contenido para la version solicitada',
        4 => 'Error accediendo al sistema de archivos',
        5 => 'Error al establecer el contenido del documento',
        6 => 'Ha ocurrido un error al intentar guardar el documento'
    );
    public $conector;
    public $nodeID;

    /**
     * Constructor de la clase
     *
     * @param int $nodeID
     */
    public function __construct(int $nodeID = null)
    {
        $this->ClearError();
        $this->nodeID = $nodeID;
    }

    /**
     * Devuelve el identificador del Nodo cargado en el objeto
     *
     * @return number|boolean
     */
    public function GetID()
    {
        $this->ClearError();
        if ($this->nodeID) {
            return $this->nodeID;
        }
        $this->SetError(1);
        return false;
    }

    /**
     * Devuelve la lista de versiones distintas para el nodo cargado en el objeto
     *
     * @param string $order
     * @return boolean|array
     */
    public function GetVersionList(string $order = 'asc')
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = new Version();
        return $versions->find('DISTINCT Version', 'IdNode = %s ORDER BY Version ' . $order, array(
            $this->nodeID
        ), MONO);
    }

    /**
     * Devuelve la lista de subversiones de una version concreta para el nodo cargado en el objeto
     *
     * @param int $version
     * @return boolean|array
     */
    public function GetSubVersionList(int $version)
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = new Version();
        return $versions->find('DISTINCT SubVersion', 'IdNode = %s AND Version = %s ORDER BY SubVersion', array(
            $this->nodeID,
            $version
        ), MONO);
    }

    /**
     * Devuelve la ultima version del nodo que hay en el objeto
     *
     * @return NULL|int
     */
    public function GetLastVersion()
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = new Version();
        $result = $versions->find('MAX(Version) AS max_version', 'IdNode = %s', array(
            $this->nodeID
        ), MONO);
        if (! $result) {
            return null;
        }
        return $result[0];
    }

    /**
     * Devuelve la ultima SubVersion del la version dada
     * 
     * @param int $version
     * @return boolean|NULL|int
     */
    public function GetLastSubVersion(int $version)
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = new Version();
        $result = $versions->find('MAX(SubVersion) as max_subversion', 'IdNode = %s AND Version = %s', array(
            $this->nodeID,
            $version
        ), MONO);
        if (! $result) {
            return null;
        }
        return $result[0];
    }

    /**
     * 
     * @param int $version
     * @param int $subversion
     * @return boolean|NULL|int
     */
    public function getVersionId(int $version, int $subversion)
    {
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = new Version();
        $result = $versions->find('IdVersion', 'Version = %s AND SubVersion = %s AND IdNode = %s', array(
            $version,
            $subversion,
            $this->nodeID
        ), MONO);
        if (! $result) {
            return null;
        }
        return $result[0];
    }

    /**
     * Devuelve si ya hay almacenada alguna version del nodo que hay en el objeto
     *
     * @return boolean|NULL|int
     */
    function HasPreviousVersions()
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = new Version();
        $result = $versions->find('COUNT(*) AS has_versions', 'IdNode = %s', array(
            $this->nodeID
        ), MONO);
        if (! $result) {
            return NULL;
        }
        return $result[0];
    }

    /**
     * Devuelve el contenido de una version
     *
     * @param int $versionID :  Si se omite este parametro y el siguiente, se tomara la ultima version del nodo en el objeto
     * @param int $subVersion : Si se omite este parametro, el anterior es el identificador unico de version, en otro caso, es el numero de Version
     * @return string|bool
     */
    public function GetContent(int $versionID = null, int $subVersion = null)
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }

        // Si no se nos especificaba la version, asumimos la ultima
        if (is_null($versionID) && is_null($subVersion)) {
            $versionID = $this->GetLastVersion();
            if (! is_null($versionID)) {
                $subVersion = $this->GetLastSubVersion($versionID);
            }
        }
        if (is_null($versionID) || is_null($subVersion)) {
            Logger::warning('Unable to estimate version or subversion');
            return false;
        }
        $uniqueName = $this->GetTmpFile($versionID, $subVersion);
        if (! $uniqueName) {
            Logger::warning('Unable to get file');
            $this->SetError(3);
            return false;
        }
        $targetPath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $uniqueName;
        $content = FsUtils::file_get_contents($targetPath);
        if ($content === false) {
            return false;
        }
        Logger::debug('GetContent for Node:' . $this->nodeID . ', Version: ' . $versionID . '.' . $subVersion . ', File: .' . $uniqueName 
            . ', Chars: ' . strlen($content));
        $node = new Node($this->nodeID);
        $isPlainFile = $node->nodeType->get('IsPlainFile');

        // Only encoding the content if the node is not one of this 3.
        if (! $isPlainFile) {

            // Look for the working encoding from Config
            $workingEncoding = App::getValue('workingEncoding');
            $content = \Ximdex\XML\Base::recodeSrc($content, $workingEncoding);
        }
        return $content;
    }

    /**
     * Devuelve el contenido metadata del structure document actual
     *
     * @deprecated
     * @param $version
     * @param $subversion
     * @return string
     */
    function GetMetadata($version = null, $subversion = null)
    {
        return $this->GetContent($version, $subversion, true);
    }

    public function _generateCaches(int $idVersion, bool $delete = false)
    {
        $res = true;
        $version = new Version($idVersion);
        if (! $version->get('IdVersion')) {
            return NULL;
        }
        $idNode = $version->get('IdNode');
        $node = new Node($idNode);
        if (! $node->get('IdNode')) {
            return NULL;
        }
        if (! $node->nodeType->GetIsStructuredDocument()) {
            return NULL;
        }

        // Delete cache if the parameter $delete is true
        $pipelineManager = new PipelineManager();
        if ($delete) {
            $pipelineManager->deleteCache($idVersion);
        }
        $channels = $node->GetChannels();
        if ($channels) {
            foreach ($channels as $idChannel) {
                Logger::info('Generation cache for version ' . $idVersion . ' and the channel ' . $idChannel);
                $data = array(
                    'CHANNEL' => $idChannel
                );
                $data['NODEID'] = $idNode;
                $data['DISABLE_CACHE'] = App::getValue('DisableCache');
                $transformer = $node->getProperty('Transformer');
                $data['TRANSFORMER'] = $transformer[0];
                if ($node->GetNodeType() == NodeTypeConstants::XML_DOCUMENT) {
                    $process = 'StrDocToDexT';
                } elseif ($node->GetNodeType() == NodeTypeConstants::HTML_DOCUMENT) {
                    $process = 'HTMLToPrepared';
                } else {
                    return false;
                }
                $res = $pipelineManager->getCacheFromProcess($idVersion, $process, $data);
            }
        }
        return $res;
    }

    /**
     * Cambia el contenido de una version
     *
     * @param string $content
     * @param int $versionID    : Si se omite este parametro y el siguiente, se tomara la ultima version del nodo en el objeto
     * @param int $subVersion   : Si se omite este parametro, el anterior es el identificador unico de version, en otro caso, es el numero de Version
     * @param boolean $commitNode
     * @return boolean|NULL|string
     */
    function SetContent(string $content, int $versionID = null, int $subVersion = null, bool $commitNode = null)
    {
        $node = new Node($this->nodeID);
        $isPlainFile = @$node->nodeType->get('IsPlainFile');

        // Only encoding the content if the node is not one of this 3
        if (! $isPlainFile) {
            
            // Look for the working encoding from Config
            $dataEncoding = App::getValue('dataEncoding');
            $content = \Ximdex\XML\Base::recodeSrc($content, $dataEncoding);
        }
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }

        // (1) No se pasa version determinada, se incrementa la version con el contenido nuevo
        if (is_null($versionID) && is_null($subVersion)) {
            $idVersion = $this->AddVersion(NULL, NULL, $content, $commitNode);
            $this->_generateCaches($idVersion);
            return $idVersion;
        }

        // (2) Se pasa version determinada y se machaca el contenido de esa version
        if (! is_null($versionID) && ! is_null($subVersion)) {
            $uniqueName = $this->GetTmpFile($versionID, $subVersion);
            if (! $uniqueName) {
                Logger::error('Error making a setContent for Node (Unable to get the file):' . $this->nodeID . ', Version: ' . $versionID . '.' 
                    . $subVersion . ', File: .' . $uniqueName . ', Chars: ' . strlen($content));
                return false;
            }
            $targetPath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $uniqueName;
            Logger::info('SetContent for Node: ' . $this->nodeID . ', Version: ' . $versionID . '.' . $subVersion . ', File: .' . $uniqueName 
                . ', Chars: ' . strlen($content));
            $result = FsUtils::file_put_contents($targetPath, $content);
            $idVersion = $this->getVersionId($versionID, $subVersion);
            if ($result && \Ximdex\Modules\Manager::isEnabled('ximRAM')) {
                $this->indexNode($idVersion, $commitNode);
            }
            $this->_generateCaches($idVersion, true);
            return $result;
        }
        return false;
    }

    /**
     * Create a new version
     * 
     * @param bool $jumpNewVersion
     * @param string $comment
     * @param string $content
     * @param bool $commitNode
     * @return boolean|NULL|string
     */
    public function AddVersion(bool $jumpNewVersion = null, string $comment = null, string $content = null, bool $commitNode = null)
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }

        // Si tiene versiones anteriores, calculamos cual es la siguiente
        if ($this->HasPreviousVersions()) {
            $curVersion = $this->GetLastVersion();
            if (is_null($curVersion)) {
                Logger::warning('Unable to get the last version of the document');
                return false;
            }
            $curSubVersion = $this->GetLastSubVersion($curVersion);
            if ($jumpNewVersion) {

                // Si queremos saltar de version x.y -> x+1.0
                $newVersion = $curVersion + 1;
                $newSubVersion = '0';
                $oldIdVersion = $this->getVersionId($curVersion, $curSubVersion);
                $oldVersion = new Version($oldIdVersion);
                if (! $oldVersion->get('IdVersion')) {
                    Logger::error('Unable to load the version with ID: ' . $oldIdVersion);
                    return false;
                }
                if (App::getValue('PurgeVersionsOnNewVersion')) {
                    $this->purgeVersions();
                }
            } else {

                // Si queremos saltar solo de subversion x.y -> x.y+1
                $newVersion = $curVersion;
                $newSubVersion = $curSubVersion + 1;
                if (App::getValue('PurgeSubversionsOnNewVersion')) {
                    $this->purgeSubVersions($newVersion);
                }
            }
            if (! $jumpNewVersion or $content !== null) {
                if (is_null($content)) {
                    $newContent = $this->GetContent($curVersion, $curSubVersion);
                } else {
                    $newContent = $content;
                }
            }
        } else {

            // Si es la primera version a guardar -> 0.0
            $newVersion = 0;
            $newSubVersion = 0;
            if (is_null($content)) {
                $newContent = '';
            } else {
                $newContent = $content;
            }
        }
        $userID = \Ximdex\Runtime\Session::get('userID');
        if (! $userID) {
            $userID = User::XIMDEX_ID;
        }
        $version = new Version();
        $version->set('IdNode', $this->nodeID);
        $version->set('Version', $newVersion);
        $version->set('SubVersion', $newSubVersion);
        $version->set('IdUser', $userID);
        $version->set('Date', time());
        $version->set('Comment', $comment);
        $IdVersion = $version->add();
        if (! $IdVersion) {
            Logger::warning('Cannot create version for Node:' . $this->nodeID . ', Version: ' . $newVersion . '.' . $newSubVersion);
            return false;
        }
        if ($jumpNewVersion and $content === null) {
            
            // Jump a new version does not create a new hash archive in the file system
            $uniqueName = $oldVersion->get('File');
        } else {
            $uniqueName = $this->getUniqueFileName($version);
            FsUtils::file_put_contents(XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $uniqueName, $newContent);
        }
        $version->set('File', $uniqueName);
        $version->update();
        Logger::debug('AddVersion for Node:' . $this->nodeID . ', Version: ' . $newVersion . '.' . $newSubVersion . ', File: ' . $uniqueName);
        return $IdVersion;
    }

    private function getUniqueFileName(Version $version) : string
    {
        $hash = FsUtils::getUniqueFile(XIMDEX_ROOT_PATH . App::getValue('FileRoot'));
        $uniqueName = $this->nodeID . '_' . $version->IdVersion . '-' . substr($hash, 5, 4);
        return $uniqueName;
    }

    /**
     * Recupera una antigua Version y la coloca como nueva
     *
     * @author Ximdex DevTeam <dev@ximdex.com>
     * @version 1.0
     * @param int $version
     * @param int $subversion
     * @param string $comment
     */
    public function RecoverVersion(?int $version, ?int $subversion, string $comment = null) : bool
    {
        $tmpVersion = $version;
        $this->ClearError();
        $node = new Node($this->nodeID);
        if (! $node->get('IdNode') || ! is_null($version) || ! is_null($subversion)) {
            $this->SetError(1);
            return false;
        }
        $purgeAll = false;

        // Siempre va a tener versiones anteriores (no tiene sentido recuperar la actual), calculamos cual es la siguiente
        $newVersion = $this->GetLastVersion();
        $curSubVersion = $this->GetLastSubVersion($newVersion);
        $purgePreviousSubVersions = App::getValue('PurgeSubversionsOnNewVersion');
        $newSubVersion = $curSubVersion + 1;

        // Le ponemos el contenido de la version que queremos recuperar
        $newContent = $this->GetContent($version, $subversion);
        $userID = \Ximdex\Runtime\Session::get('userID');

        // Ejecutamos la insercion en la BD
        $version = new Version();
        $version->set('IdNode', $this->nodeID);
        $version->set('Version', $newVersion);
        $version->set('SubVersion', $newSubVersion);
        $version->set('IdUser', $userID);
        $version->set('Date', time());
        $version->set('Comment', $comment);
        $IdVersion = $version->add();
        $fileName = $node->class->GetNodePath();
        $fileContent = $node->class->GetRenderizedContent();
        $nodetype = new \Ximdex\Models\NodeType($node->GetNodeType());
        
        // Se guarda en un archivo de id unico
        $uniqueName = $this->getUniqueFileName($version);
        $targetPath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $uniqueName;
        if (! FsUtils::file_put_contents($targetPath, $newContent)) {
            Logger::error('failed to set document content');
            $this->SetError(5);
        }
        $version->set('File', $uniqueName);
        $version->update();

        // / Lo guardamos en el sistema de archivos
        if ($nodetype->GetHasFSEntity() && ! FsUtils::file_put_contents($fileName, $fileContent)) {
            Logger::error('An error occurred while trying to save the document');
            $this->SetError(6);
            return false;
        }
        Logger::debug('RecoverVersion for Node' . $this->nodeID . ' with result:' . $IdVersion . ', Version: ' . $newVersion . '.' 
            . $newSubVersion . ', OldVersion: ' . $tmpVersion . '.' . $subversion . ', File: .' . $uniqueName, 4, 'DataFactory');
        $purgePreviousSubVersions = App::getValue('PurgeSubversionsOnNewVersion');
        if ($purgePreviousSubVersions && $this->HasPreviousVersions()) {
            $this->purgeSubVersions($newVersion, $purgeAll);
        }
        return true;
    }

    /**
     * Elimina todas las Versiones del Nodo
     *
     * @return bool
     */
    function DeleteAllVersions()
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $versions = $this->GetVersionList();
        if (! is_array($versions)) {
            return false;
        }
        foreach ($versions as $version) {
            $this->deleteVersion($version);
        }
        return true;
    }

    /**
     * Elimina todas las subVersiones de la Version dada
     *
     * @param int $version
     * @return bool
     */
    public function deleteVersion(int $version): bool
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $subVersions = $this->GetSubVersionList($version);
        if (! is_array($subVersions)) {
            return false;
        }
        foreach ($subVersions as $subVersion) {
            $this->deleteSubVersion($version, $subVersion);
        }
        return true;
    }

    /**
     * Elimina una SubVersion
     * versionID: Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * El parametro versionID significa el campo IdVersion de la tabla versions si la funcion recibe $subVersions == null
     * El parametro versionID siginifica el campo Version de la tabla Versions si la funcion recibe $subVersions != null
     *
     * @param int $versionID
     * @param int $subVersion
     * @return bool
     */
    public function deleteSubversion(int $versionID, int $subVersion = null): bool
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $uniqueName = $this->GetTmpFile($versionID, $subVersion);
        if (! $uniqueName) {
            $this->SetError(3);
            return false;
        }
        if (is_null($subVersion)) {
            $query = sprintf('DELETE FROM Versions WHERE IdVersion = %d AND IdNode = %d', $versionID, $this->nodeID);
            $versionToDelete = $versionID;
        } else {
            $query = sprintf('DELETE FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d', $versionID, $subVersion, $this->nodeID);
            $versionToDelete = $this->getVersionId($versionID, $subVersion);
        }

        // If the file is in use for another version, do not delete it
        $version = new Version();
        $res = $version->find('IdVersion', 'File = \'' . $uniqueName . '\' and IdVersion != ' . $versionToDelete);
        if (! $res) {
            $targetPath = XIMDEX_ROOT_PATH . App::getValue('FileRoot') . '/' . $uniqueName;

            /*
             * Tal y como estaba el codigo dejaba sucia la base de datos
             * si se borraba el archivo manualmente o simplemente no se podia borrar por permisos
             */
            if (is_file($targetPath)) {
                FsUtils::delete($targetPath);
            }
        }

        // Deleting cache
        Logger::info('Deleting cache from versionId ' . $versionToDelete);
        $pipeline = new PipelineManager();
        $pipeline->deleteCache($versionToDelete);
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Execute($query);
        Logger::info('DeleteVersion for Node:' . $this->nodeID . ', Version: ' . $versionID . '.' . $subVersion . ', File: .' . $uniqueName);
        return true;
    }

    /**
     * Elimina las subVersiones de una determinada version que ya nos sirven.
     * Tiene dos casos de uso, dependiendo del parametro all.
     * 1) $all = false => Elimina todas las subVersiones de la Version dada menos la primera, las X ultimas segun la tabla
     * de configuracion (X >= 1).
     * 2) $all = true => Elimina todas las subVersiones de la Version dada menos la primera.
     *
     * @param int $version
     * @param bool $all
     * @return bool
     */
    public function purgeSubVersions(int $version, bool $all = false): bool
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $maxSubVersionsAllowed = App::getValue('MaxSubVersionsAllowed');
        if ($maxSubVersionsAllowed <= 0) {
            $maxSubVersionsAllowed = 1;
        }
        $subVersions = $this->GetSubVersionList($version);
        if (! is_array($subVersions)) {
            $subVersions = array();
        }
        array_shift($subVersions);
        if (! $all) {
            for ($i = 0; $i < $maxSubVersionsAllowed - 1; $i ++) {
                if (count($subVersions)) {
                    array_pop($subVersions);
                }
            }
        }
        if ($subVersions) {
            foreach ($subVersions as $subVersion) {
                $this->deleteSubVersion($version, $subVersion);
            }
        }
        return true;
    }

    private function purgeVersions(): bool
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $maxVersionsAllowed = App::getValue('MaxVersionsAllowed');
        if ($maxVersionsAllowed <= 0) {
            $maxVersionsAllowed = 1;
        }
        $versions = $this->GetVersionList();
        for ($i = 0; $i < $maxVersionsAllowed; $i ++) {
            if (count($versions) > 0)
                array_pop($versions);
        }
        if (is_array($versions)) {
            foreach ($versions as $version) {
                $this->deleteVersion($version);
            }
        }
        return true;
    }

    /**
     * Devuelve el nombre del archivo temporal en el que se guarda el contenido de una SubVersion
     *
     * @param int $versionID : Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param int $subVersion
     * @return boolean|NULL|string
     */
    private function GetTmpFile(int $versionID, int $subVersion = null)
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return false;
        }
        $node = new Node($this->nodeID);
        $parentId = $node->GetParent();
        unset($node);
        $dbObj = new \Ximdex\Runtime\Db();
        if (is_null($subVersion)) {
            $query = sprintf('SELECT File FROM Versions v INNER JOIN Nodes n on v.IdNode = n.Idnode AND n.IdParent = %d 
                WHERE IdVersion = %d AND v.IdNode = %d', $parentId, $versionID, $this->nodeID);
        } else {
            $query = sprintf('SELECT File FROM Versions v INNER JOIN Nodes n on v.IdNode = n.Idnode AND n.IdParent = %d 
                WHERE Version = %d AND SubVersion = %d AND v.IdNode = %d', $parentId, $versionID, $subVersion, $this->nodeID);
        }
        $dbObj->Query($query);
        $uniqueName = ! $dbObj->EOF ? $dbObj->GetValue('File') : false;
        if (! $uniqueName) {
            $this->SetError(3);
            return false;
        }
        return $uniqueName;
    }

    /**
     * Devuelve el comentario de una SubVersion
     *
     * @param int $versionID : Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param int $versionID
     * @param $subVersion
     * @return NULL|string
     */
    public function GetComment(int $versionID, int $subVersion = null) : ?string
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return null;
        }
        $dbObj = new \Ximdex\Runtime\Db();
        if (is_null($subVersion)) {
            $query = sprintf('SELECT Comment FROM Versions WHERE IdVersion = %d AND IdNode = %d', $versionID, $this->nodeID);
        } else {
            $query = sprintf('SELECT Comment FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d', $versionID
                , $subVersion, $this->nodeID);
        }
        $dbObj->Query($query);
        return $dbObj->GetValue('Comment');
    }

    /**
     * Devuelve el la fecha de almacenamiento de una SubVersion
     *
     * @param int $versionID : Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param int $versionID
     * @param int $subVersion
     * @return NULL|string
     */
    public function GetDate(int $versionID, int $subVersion = null) : ?string
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return NULL;
        }
        $dbObj = new \Ximdex\Runtime\Db();
        if (is_null($subVersion)) {
            $query = sprintf('SELECT Date FROM Versions WHERE IdVersion = %d AND IdNode = %d', $versionID, $this->nodeID);
        } else {
            $query = sprintf('SELECT Date FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d', $versionID
                , $subVersion, $this->nodeID);
        }
        $dbObj->Query($query);
        return $dbObj->GetValue('Date');
    }

    /**
     * Devuelve el id del usuario que gestiono la version
     *
     * @param int $versionID : Si se omite el siguiente parametro, este es el identificador unico de version, en otro caso, es el numero de version
     * @param int $subVersion
     * @return NULL|string
     */
    public function GetUserID($versionID, $subVersion = null) : ?string
    {
        $this->ClearError();
        if (! $this->nodeID) {
            $this->SetError(1);
            return NULL;
        }
        $dbObj = new \Ximdex\Runtime\Db();
        if (is_null($subVersion)) {
            $query = sprintf('SELECT IdUser FROM Versions WHERE IdVersion = %d AND IdNode = %d', $versionID, $this->nodeID);
        } else {
            $query = sprintf('SELECT IdUser FROM Versions WHERE Version = %d AND SubVersion = %d AND IdNode = %d', $versionID
                , $subVersion, $this->nodeID);
        }
        $dbObj->Query($query);
        return $dbObj->GetValue('IdUser');
    }

    /**
     * Se queda de wrapper
     *
     * @param int $versionID
     * @return boolean|array
     */
    public function GetVersionAndSubVersion(int $versionID)
    {
        $version = new Version($versionID);
        if (! $version->get('IdVersion')) {
            return false;
        }
        return array(
            $version->get('Version'),
            $version->get('SubVersion')
        );
    }

    /**
     * Solo se usa en el script devel/scripts/ficheros_sobrantesIO.php
     *
     * @return number|NULL|string
     */
    public function GetFiles()
    {
        $query = 'SELECT File FROM Versions';
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query($query);
        if (! $dbObj->numRows) {
            return 0;
        }
        if (! $dbObj->numErr) {
            $array_files = [];
            while (! $dbObj->EOF) {
                $array_files[] = $dbObj->GetValue('File');
                $dbObj->Next();
            }
            return $array_files;
        }
        $this->SetError(4);
        return 0;
    }

    /**
     * Resetea el flag de ocurrencia de error
     */
    public function ClearError()
    {
        $this->numErr = null;
        $this->msgErr = null;
    }

    /**
     * Carga en el objeto el codigo del ultimo error ocurrido
     *
     * @param int $code
     */
    public function SetError(int $code)
    {
        $this->numErr = $code;
        $this->msgErr = $this->errorList[$code];
    }

    /**
     * Devuelve un booleano que indica si hubo algun error
     *
     * @return NULL|int
     */
    public function HasError() : ?int
    {
        return $this->numErr;
    }

    /**
     * Devuelve el idversion correspondiente a la version publicada actualmente
     *
     * @return boolean|string
     */
    public function GetPublishedIdVersion()
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $this->ClearError();
        if ($this->nodeID) {
            $query = sprintf('SELECT MAX(IdVersion) AS max_version FROM Versions WHERE SubVersion = 0 AND IdNode = %d', $this->nodeID);
            $dbObj->Query($query);
            $idVersion = $dbObj->GetValue('max_version');
            $version = new Version($idVersion);
            if ($version->get('IdVersion') && $version->get('Version')) {
                $sync = new SynchroFacade();
                if ($sync->isNodePublished($this->nodeID)) {
                    return $version->get('IdVersion');
                }
            }
        }
        return false;
    }

    public function GetLastVersionId() : ?int
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $this->ClearError();
        if (! is_null($this->nodeID)) {
            $dbObj->Query('SELECT MAX(IdVersion) FROM Versions WHERE IdNode = ' . $this->nodeID);
            $version = $dbObj->GetValue('MAX(IdVersion)');
            return $version;
        }
        $this->SetError(1);
    }

    public function GetVersionFromId(int $idVersion) : array
    {
        $dbObj = new \Ximdex\Runtime\Db();
        $dbObj->Query('SELECT Version, SubVersion FROM Versions WHERE IdVersion = ' . $idVersion);
        $version = array();
        while (! $dbObj->EOF) {
            $version['version'] = $dbObj->GetValue('Version');
            $version['subversion'] = $dbObj->GetValue('SubVersion');
            $dbObj->Next();
        }
        return $version;
    }

    public function GetPreviousVersion(int $idVersion) : ?int
    {
        if (! $this->nodeID) {
            return null;
        }
        $versions = new Version();
        $result = $versions->find('Max(IdVersion)', 'IdNode = %s AND IdVersion < %s', array(
            $this->nodeID,
            $idVersion
        ), MONO);
        if (! $result) {
            return null;
        }
        return $result[0];
    }

    public function indexNode(int $idVersion, bool $commitNode)
    {
        if (! is_numeric($idVersion)) {
            Logger::warning('Attempted to index a node by an invalid IdVersion.');
            return;
        }
        $usePool = (bool) App::getValue('AddVersionUsesPool');
        if (! $usePool) {
            $this->conector->indexNode($idVersion, $commitNode);
        }
    }

    private function updateCaches(int $oldIdVersion, int $idVersion)
    {
        if (! App::getValue('DisableCache')) {
            $pipeCache = new PipeCache();
            return $pipeCache->upgradeCaches($oldIdVersion, $idVersion);
        }
    }
}
