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

require_once (XIMDEX_ROOT_PATH . '/inc/io/connection/I_Connector.class.php');
require_once (XIMDEX_ROOT_PATH . '/extensions/solarium/solarium/library/Solarium/Autoloader.php');
Solarium_Autoloader::register();

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
          'adapteroptions' => array(
              'host' => $host,
              'port' => $port,
//              'core' => 'solr' //defined in method 'put'
          ),
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

      // guess idnode from server path
      $pathParts = explode("/", $path);

      $docNameFull = array_pop($pathParts);
      $nameSplit = explode("-", $docNameFull);
      $docName = $nameSplit[0];

      $partsJoin = implode("/", $pathParts);
      $partialTablePath = "{$partsJoin}/documents/{$docName}";

      $node = new Nodes_ORM();
      $result = $node->find('idnode', "Path REGEXP %s", array($partialTablePath), MONO);

      if (!isset($result[0])) {
         XMD_Log::error("unexpected result, document may have not been deleted");
         return false;
      }

      // delete solr document using id
      $client = new Solarium_Client($this->config);
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
      $core = array_shift(explode("/", $targetFile));
      $this->config['adapteroptions']['core'] = $core;

      $xml = simplexml_load_file($localFile);
      if (!$xml) {
         XMD_Log::error("invalid xml file: $localFile");
         return false;
      }

      //Adapt xml to Solarium
      $client = new Solarium_Client($this->config);
      if (!$client) {
         XMD_Log::error("fail to create a Solarium_Client instance");
         return false;
      }
      $update = $client->createUpdate();
      $doc = $update->createDocument();
      $publicationPath = array();
      $tags = array();

      // Adapt all attributes except "id" and "names"
      foreach ($xml->children() as $field) {
         $attr = $field->attributes();
         $key = (string) $attr["name"];

         if ($key === "id") {
            $nodeID = $field;
         } elseif ($key === "names") {
            foreach ($field->children() as $partialPath) {
               $publicationPath[] = (string) $partialPath;
            }
            continue;
         } elseif ($key === "tags") {
            $tagAndTypes = explode(",", $field);

            $getTag = function($tagWithType) {
               $tagStr = explode(":", $tagWithType);
               return $tagStr[0];
            };

            $tags = array_map($getTag, $tagAndTypes);
            continue;
         }
         $doc->addField($key, $field);
      }

      // tags
      foreach ($tags as $tag) {
         $doc->addField('tags', $tag);
      }

      // check if the document already exists, set "creationdate"
      $CREATION_DATE_FIELD = 'creationdate';
      $selectQuery = $client->createSelect();
      $selectQuery->setQuery("id:$nodeID");
      $selectQuery->setFields(array($CREATION_DATE_FIELD));
      $resultset = $client->select($selectQuery);
      $creationdate = "";
      if ($resultset->getNumFound() !== 0) {
         foreach ($resultset as $res) {
            foreach ($res as $field => $value) {
               if ($field === $CREATION_DATE_FIELD) {
                  $creationdate = $value;
                  break;
               }
            }
            break;
         }
      } else {
         $creationdate = date("Y-m-d\TH:i:s\Z");
      }
      $doc->addField($CREATION_DATE_FIELD, $creationdate);

      // Retrieve server that belongs to node's project and has channel html 
      $node = new Node($nodeID);
      $idChannelHtml = "10001";
      $publicationPath[] = $node->GetNodeName();
      $relServers = new RelServersChannels();
      $htmlServers = $relServers->find('IdServer', 'IdChannel = %s', array($idChannelHtml), MULTI);
      $queryParams = array();
      $inSqlSectionArray = array();
      foreach ($htmlServers as $s) {
         $queryParams[] = $s["IdServer"];
         $inSqlSectionArray[] = "%s";
      }
      $dbServer = new Server();
      $inSqlSection = implode(",", $inSqlSectionArray);
      $queryParams[] = $node->getServer();
      $myServer = $dbServer->find('Url', "IdServer in ($inSqlSection) and IdNode = %s", $queryParams, MONO);

      // Use full url in solr
      array_unshift($publicationPath, $myServer[0]);
      $publicationUrl = implode("/", $publicationPath) . "-idhtml.html";
      $doc->addField("publicationUrl", $publicationUrl);

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
      XMD_Log::info("<< Solr update ok >>");
      return true;
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