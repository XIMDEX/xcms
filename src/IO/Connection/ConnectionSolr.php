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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

namespace Ximdex\IO\Connection;

use Ximdex\Logger;
use Ximdex\Models\ORM\NodesOrm as Nodes_ORM;
use Ximdex\Models\RelSemanticTagsNodes;
use Exception;

class ConnectionSolr extends Connector implements IConnector
{
    private $connected = false;
    private $validExtensions = array('xml' => 1, 'pdf' => 1, 'epub' => 1);
    private $config;

    /**
     * Connect to server
     * Send ping to servidor to ensure it is active.
     *
     * @access public
     * @param host string
     * @param port int
     * @return boolean
     */
    public function connect($host = null, $port = null)
    {
        Logger::debug("CONNECT $host $port");
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
    public function disconnect()
    {
        $this->connected = false;
        return true;
    }

    /**
     * Check the status of the connection
     */
    public function isConnected()
    {
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
    public function login($username = 'anonymous', $username = 'john.doe@example.com')
    {
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
    public function cd($dir)
    {
        return true;
    }

    /**
     * Get the server folder. UNUSED.
     *
     * @access public
     * @param dir string
     * @return string
     */
    public function pwd()
    {
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
    public function mkdir($dir, $mode = 0755, $recursive = false)
    {
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
    public function chmod($target, $mode = 0755, $recursive = false)
    {
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
    public function rename($renameFrom, $renameTo)
    {
        Logger::debug("RENAME $renameFrom -> $renameTo");
        return true;
    }

    /**
     * Get the size of a file. UNUSED.
     *
     * @access public
     * @param file string
     * @return int
     */
    public function size($file)
    {
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
    public function ls($dir, $mode = null)
    {
        return array();
    }

    /**
     * Checks if the especified path is a folder. UNUSED.
     *
     * @access public
     * @param path string
     * @return boolean
     */
    public function isDir($path)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::isFile()
     */
    public function isFile($path)
    {
        return false;
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
    public function get($sourceFile, $targetFile, $mode = 0755)
    {
        Logger::debug("GET $sourceFile, $targetFile");
        return false;
    }

    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::rm()
     */
    public function rm($path, int $id = null)
    {
        Logger::debug("RM $path");
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
            Logger::error("unexpected result, document may have not been deleted");
            Logger::error(print_r(array(
                "qName" => $qName,
                "qPath" => $qPath), true));
            return false;
        }

        // Delete solr document using id
        $client = new \Solarium\Client($this->config);
        $update = $client->createUpdate();
        $update->addDeleteById($result[0]);
        $update->addCommit();
        $solrResp = $client->update($update);
        if ($solrResp->getStatus() !== 0) {
            Logger::error("solr error deleting doc id: {$result[0]}");
            return false;
        }
        Logger::debug("solr doc id: {$result[0]} was deleted");
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
    public function put($localFile, $targetFile, $mode = 0755)
    {
        Logger::debug("PUT $localFile TO $targetFile");
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
            
            //Don't publish binary files
            return true;
            return $this->putBinaryFile($localFile, $pathInfo);
        }
    }

    public function putXmlFile($localFile, $pathInfo)
    {
        Logger::debug("putXmlFile");
        
        // Load xml coming from transformation
        $xml = simplexml_load_file($localFile);
        if (!$xml) {
            Logger::error("invalid xml file: $localFile");
            return false;
        }

        // Create client
        try {
            $client = new \Solarium\Client($this->config);
        } catch (Exception $e) {
            Logger::error("fail to create a Solarium_Client instance");
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
            Logger::error("Exception: {$e->getMessage()}");
            return false;
        }
        if ($result->getStatus() !== 0) {
            Logger::error("<< Solr update error - status: {$result->getStatus()} >>");
            return false;
        }
        Logger::debug("<< Solr put xml ok - id: {$doc->id} >>");
        return true;
    }

    public function putBinaryFile($localFile, $pathInfo)
    {
        Logger::debug("putBinaryFile - $localFile - " . $pathInfo["fullName"]);
        $trueName = $this->extractNodeNameBinaryPut($pathInfo["fullName"]);
        
        // Get node id
        $node = new Nodes_ORM();
        $result = $node->find('idnode', "Name = %s AND Path REGEXP %s", array($trueName, $pathInfo["subPath"] . '$'), MONO);
        if (!isset($result[0])) {
            Logger::error(sprintf("NOT found: Name = %s AND Path REGEXP %s", $trueName, $pathInfo["subPath"] . '$'));
            return false;
        }
        
        // Create an extract query instance and add settings
        $client = new \Solarium\Client($this->config);
        $query = $client->createExtract();
        $query->setFile($localFile);
        $query->setCommit(true);
        $query->setOmitHeader(false);
        
        // Add document
        $doc = $query->createDocument();
        $doc->id = $result[0];

        // Add tags if attached to ximdex document
        $relTag = new RelSemanticTagsNodes();
        $tags = $relTag->getTags($result[0]);
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $doc->addField("tags", $tag["Name"]);
            }
        }
        
        // This executes the query and returns the result
        Logger::debug(print_r($doc->getFields(), true));
        $query->addParam('lowernames', 'false');
        $query->setDocument($doc);
        $resultExtract = $client->extract($query);
        if ($resultExtract->getStatus() !== 0) {
            Logger::error("<< Solr update error - status: {$resultExtract->getStatus()} >>");
            return false;
        }
        Logger::debug("<< Solr put binary ok - id: {$doc->id} >>");
        return true;
    }

    public function getNameExtension($name)
    {
        $arr = explode(".", $name);
        $ext = array_pop($arr);
        return $ext;
    }

    public function splitPath($path)
    {
        $arr = explode("/", $path);
        $core = array_shift($arr);
        $fullName = array_pop($arr);
        $subPath = implode('/', $arr);
        return array("core" => $core,
            "subPath" => $subPath,
            "fullName" => $fullName);
    }

    public function extractNodeName($fullName, $withIdiom = false)
    {
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

    public function loadAdditionalFields($doc, $client, $core)
    {
        // Data folders are not commited, so must be configured per project
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
            if ($fieldCfg["STORE"] === 'ALWAYS') {  // Calculate value
                $fieldValue = $fieldCfg["FUNCTION"]();
            } else {    // "IF_NEW"
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
                if ($fieldValue === "") {   // Calculate value
                    $fieldValue = $fieldCfg["FUNCTION"]();
                }
            }
            $doc->addField($fieldname, $fieldValue);
        }
    }

    public function extractNodeNameBinaryPut($fullName)
    {
        $fullNameParts = explode("_", $fullName, 2);
        array_shift($fullNameParts);
        $trueName = implode("", $fullNameParts);
        return $trueName;
    }
    
    /**
     * {@inheritDoc}
     * @see \Ximdex\IO\Connection\IConnector::dirIsEmpty()
     */
    public function dirIsEmpty(string $path): bool
    {
        return false;
    }
}
