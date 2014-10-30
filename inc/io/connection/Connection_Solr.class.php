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
if (!defined('XIMDEX_ROOT_PATH')) {
    define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../'));
}

require_once (XIMDEX_ROOT_PATH . '/modules/ximNOTA/model/RelNodeMetaData.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/io/connection/I_Connector.class.php');

class Connection_Solr implements I_Connector {

    private $connected = false;
    private $config;

    /**
     * Connect to server.
     * Send ping to servidor to ensure it is active.
     * 
     * @access public
     * @param host string
     * @param port int
     * @return boolean
     */
    public function connect($host = NULL, $port = NULL) {
        XMD_Log::info("CONNECT $host $port");

        $this->config = array(
            'endpoint' => array(
                'localhost' => array(
                    'host' => $host,
                    'port' => $port,
                    'path' => '/solr/',
                )
            )
        );

        $this->connected = true;

        return true;
    }

    /**
     * Disconnect from server
     * 
     * @access public
     * @return boolean
     */
    public function disconnect() {
        $this->connected = false;
        return true;
    }

    /**
     * Check the status of the connection
     *
     */
    public function isConnected() {
        return $this->connected;
    }

    /**
     * Authenticate into server.
     * Useless in solr. Connection status is done in connect method.
     * 
     * @access public
     * @param login string
     * @param password string
     * @return boolean
     */
    public function login($username = 'anonymous', $username = 'john.doe@example.com') {
        return true;
    }

    /**
     * Change directory in server.
     * CHECK scheduler: is called several times.
     * 
     * @access public
     * @param dir string
     * @return boolean false if folder no exist
     */
    public function cd($dir) {
        return true;
    }

    /**
     * Get the server folder. UNUSED.
     * 
     * @access public
     * @param dir string
     * @return string
     */
    public function pwd() {
        return "";
    }

    /**
     * Create a folder in the server. UNUSED.
     * 
     * @access public
     * @param dir string
     * @param mode int
     * @param recursive boolean
     * @return boolean
     */
    public function mkdir($dir, $mode = 0755, $recursive = false) {
        return true;
    }

    /**
     * Manage permissions on a file/folder. UNUSED.
     * 
     * @access public
     * @param target string
     * @param mode string
     * @param recursive boolean
     * @return boolean
     */
    public function chmod($target, $mode = 0755, $recursive = false) {
        return false;
    }

    /**
     * Rename a file in the server. UNUSED.
     * 
     * @access public
     * @param renameFrom string
     * @param renameTo string
     * @return boolean
     */
    public function rename($renameFrom, $renameTo) {
        XMD_Log::info("RENAME $renameFrom -> $renameTo");
        return true;
    }

    /**
     * Get the size of a file. UNUSED.
     * 
     * @access public
     * @param file string
     * @return int
     */
    public function size($file) {
        return 0;
    }

    /**
     * Get the folder contents. UNUSED.
     * 
     * @access public
     * @param dir string
     * @param mode int
     * @return mixed
     */
    public function ls($dir, $mode = NULL) {
        return array();
    }

    public function splitPath($path) {
        XMD_Log::info("splitPath");
        $arr = explode("/", $path);
        $core = array_shift($arr);
        $fullName = array_pop($arr);
        $subPath = implode('/', $arr);
        return array("core" => $core,
            "subPath" => $subPath,
            "fullName" => $fullName);
    }

    public function getNameExtension($name) {
        $arr = explode(".", $name);
        $ext = array_pop($arr);
        return $ext;
    }

    public function extractNodeName($fullName, $withIdiom = false) {
        $name = "";
        $fullNameParts = explode("_", $fullName);
        $nameNoServerFrame = implode("", array($fullNameParts[0], $fullNameParts[1]));
        if ($this->getNameExtension($fullName) === "xml") {
            $nameNoServerFrameParts = explode("-", $nameNoServerFrame);
            if ($withIdiom) {
                $name = implode("-", array($nameNoServerFrameParts[0], $nameNoServerFrameParts[1]));
            } else {
                $name = $nameNoServerFrameParts[0];
            }
        } else {
            $name = $nameNoServerFrame;
        }

        return $name;
    }

    public function extractNodeNameBinaryPut($fullName) {
        $fullNameParts = explode("_", $fullName, 2);
        array_shift($fullNameParts);
        $trueName = implode("", $fullNameParts);
        return $trueName;
    }

    /**
     * Removes a file from server
     * 
     * @access public
     * @param path string
     * @param recursive boolean
     * @param filesOnly boolean
     * @return boolean
     */
    public function rm($path) {
        XMD_Log::info("RM $path");

        $pathInfo = $this->splitPath($path);
        $this->config['endpoint']['localhost']['core'] = $pathInfo["core"];

        if ($this->getNameExtension($pathInfo["fullName"]) === "xml") {
            $nodeNameNoIdiom = $this->extractNodeName($pathInfo["fullName"]);
            $qPath = implode("/", array($pathInfo["subPath"], "documents", $nodeNameNoIdiom));
            $qName = $this->extractNodeName($pathInfo["fullName"], true);
        } else {
            $qPath = $pathInfo["subPath"];
            $qName = $pathInfo["fullName"];
        }

        $node = new Nodes_ORM();
        $result = $node->find('idnode', "Name = %s AND Path REGEXP %s", array($qName, $qPath), MONO);

        if (!isset($result[0])) {
            XMD_Log::error("unexpected result, document may have not been deleted");
            XMD_Log::error(print_r(array(
                "qName" => $qName,
                "qPath" => $qPath), true));
            return false;
        }

        // delete solr document using id
        $client = new Solarium\Client($this->config);
        $update = $client->createUpdate();
        $update->addDeleteById($result[0]);
        $update->addCommit();
        $solrResp = $client->update($update);

        if ($solrResp->getStatus() !== 0) {
            XMD_Log::error("solr error deleting doc id: {$result[0]}");
            return false;
        }

        XMD_Log::info("solr doc id: {$result[0]} was deleted");
        return true;
    }

    /**
     * Copies a file from server to local. UNUSED.
     * 
     * @access public
     * @param remoteFile string
     * @param localFile string
     * @param overwrite boolean
     * @param mode
     * @return boolean
     */
    public function get($sourceFile, $targetFile, $mode = 0755) {
        XMD_Log::info("GET $sourceFile, $targetFile");
        return false;
    }

    public function loadAdditionalFields($doc, $client, $core) {
        // data folders are not commited, so must be configured per project.
        $fieldsConf = include(XIMDEX_ROOT_PATH . "/data/solr-core/{$core}/solr_additional_fields.conf");
        if (empty($fieldsConf) || count($fieldsConf) == 0) {
            return;
        }

        $fieldnameArray = array_keys($fieldsConf);
        $selectQuery = $client->createSelect();
        $selectQuery->setQuery("id:" . $doc["id"]);
        $selectQuery->setFields($fieldnameArray);
        $resultset = $client->select($selectQuery);

        foreach ($fieldnameArray as $fieldname) {
            $fieldCfg = $fieldsConf[$fieldname];
            $fieldValue = "";
            if ($fieldCfg["STORE"] === 'ALWAYS') { //calculate value
                $fieldValue = $fieldCfg["FUNCTION"]();
            } else { //"IF_NEW"
                if ($resultset->getNumFound() !== 0) {
                    foreach ($resultset as $document) {
                        foreach ($document as $field => $value) {
                            if ($field === $fieldname) {
                                $fieldValue = $value;
                                break;
                            }
                        }
                        break;
                    }
                }
                if ($fieldValue === "") { //calculate value
                    $fieldValue = $fieldCfg["FUNCTION"]();
                }
            }

            $doc->addField($fieldname, $fieldValue);
        }
    }

    public function putXmlFile($localFile, $pathInfo) {
        XMD_Log::info("putXmlFile");
        // Load xml coming from transformation
        $xml = simplexml_load_file($localFile);
        if (!$xml) {
            XMD_Log::error("invalid xml file: $localFile");
            return false;
        }

        // Create client
        try {
            $client = new Solarium\Client($this->config);
        } catch (Exception $e) {
            XMD_Log::error("fail to create a Solarium_Client instance");
            return false;
        }

        // Adapt all attributes to Solarium
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        foreach ($xml->children() as $field) {
            $attr = $field->attributes();
            $key = (string) $attr["name"];
            $doc->addField($key, $field);
        }

        // Add additional fields according to conf file.
        // It may be used to change/delete fields.
        $this->loadAdditionalFields($doc, $client, $pathInfo["core"]);
        $update->addDocument($doc);
        $update->addCommit();
        try {
            $result = $client->update($update);
        } catch (Exception $e) {
            XMD_Log::error("Exception: {$e->getMessage()}");
            return false;
        }

        if ($result->getStatus() !== 0) {
            XMD_Log::error("<< Solr update error - status: {$result->getStatus()} >>");
            return false;
        }

        return true;
    }

    public function putBinaryFile($localFile, $pathInfo) {
        XMD_Log::info("putBinaryFile - $localFile - " . $pathInfo["fullName"]);
        $trueName = $this->extractNodeNameBinaryPut($pathInfo["fullName"]);

        // get node id
        $node = new Nodes_ORM();
        $result = $node->find('idnode', "Name = %s AND Path REGEXP %s", array($trueName, $pathInfo["subPath"] . '$'), MONO);
        if (!isset($result[0])) {
            XMD_Log::error(sprintf("NOT found: Name = %s AND Path REGEXP %s", $trueName, $pathInfo["subPath"] . '$'));
            return false;
        }

        // create an extract query instance and add settings
        $client = new Solarium\Client($this->config);
        $query = $client->createExtract();
        $query->setFile($localFile);
        $query->setCommit(true);
        $query->setOmitHeader(false);

        // add document
        $doc = $query->createDocument();
        $doc->id = $result[0];

        // add tags if attached to ximdex document
        $relTag = new RelTagsNodes();
        $tags = $relTag->getTags($result[0]);
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $doc->addField("tags", $tag["Name"]);
            }
        }

        // add xml metadata fields if the file exists
        $relNodeMetaData = new RelNodeMetaData();
        $idMetadata = $relNodeMetaData->find('idMetadata', 'idNode = %s', array($result[0]), MONO);
        if (isset($idMetadata[0])) {
            XMD_Log::info("found node metadata");
            $metaNode = new Node($idMetadata[0]);
            $content = $metaNode->GetContent();
            XMD_Log::info("loaded node metadata");
            $Resolucion = new SimpleXMLElement($content);
            if ($Resolucion) {
                XMD_Log::info("parsed node metadata");
                $metaFieldsConf = include_once(XIMDEX_ROOT_PATH . "/data/solr-core/{$pathInfo["core"]}/metadata/metadata.conf");
                if (!empty($metaFieldsConf) && count($metaFieldsConf) > 0) {
                    foreach ($metaFieldsConf as $xmlPath => $mapSolrField) {
                        // check if path exist in xml object
                        $xmlPathParts = explode('/', $xmlPath);
                        $thisLevel = $Resolucion;
                        $existsNode = false;
                        foreach ($xmlPathParts as $subLevel) {
                            if (isset($thisLevel->{$subLevel})) {
                                $thisLevel = $thisLevel->{$subLevel};
                                $existsNode = true;
                            } else {
                                $existsNode = false;
                                break;
                            }
                        }
                        if ($existsNode) {
                            $thisLevelContent = trim((string) $thisLevel);
                            if (strlen($thisLevelContent) > 0) {
                                $doc->addField($mapSolrField, $thisLevelContent);
                            }
                        }
                    }
                }
            } else {
                XMD_Log::error("invalid xml metadata file. node id: " . $idMetadata[0]);
            }
        }

        // this executes the query and returns the result
        XMD_Log::info(print_r($doc->getFields(), true));
        $query->addParam('lowernames', 'false');
        $query->setDocument($doc);
        $resultExtract = $client->extract($query);
        if ($resultExtract->getStatus() !== 0) {
            XMD_Log::error("<< Solr update error - status: {$resultExtract->getStatus()} >>");
            return false;
        }

        return true;
    }

    /**
     * Copies a file from local to server.
     * 
     * @access public
     * @param localFile string
     * @param remoteFile string
     * @param overwrite boolean
     * @param mode
     * @return boolean
     */
    public function put($localFile, $targetFile, $mode = 0755) {
        XMD_Log::info("PUT $localFile TO $targetFile");

        $pathInfo = $this->splitPath($targetFile);
        $this->config['endpoint']['localhost']['core'] = $pathInfo["core"];

        // Get file extension
        $targetFileParts = explode("/", $targetFile);
        $filename = array_pop($targetFileParts);
        $fileParts = explode(".", $filename);
        $fileExtension = array_pop($fileParts);

        if ($fileExtension === "xml") {
            return $this->putXmlFile($localFile, $pathInfo);
        } else {
            return $this->putBinaryFile($localFile, $pathInfo);
        }
    }

    /**
     * Checks if the especified path is a folder. UNUSED.
     * 
     * @access public
     * @param path string
     * @return boolean
     */
    public function isDir($path) {
        return false;
    }

    /**
     * Checks if the especified path is a file. UNUSED.
     * 
     * @access public 
     * @param path string
     * @return boolean
     */
    public function isFile($path) {
        return false;
    }

}

?>
