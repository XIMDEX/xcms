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




ModulesManager::file('/inc/model/orm/XimNewsCache_ORM.class.php', 'ximNEWS');
ModulesManager::file('/inc/xml/XSLT.class.php');
ModulesManager::file('/inc/helper/Messages.class.php');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');


/**
*   @brief Handles the News cache.
*
*   This cache stores the piece of the xml of News that is inserted into the Bulletin.
*/

class XimNewsCache extends XimNewsCache_ORM {

	/**
	*  Creates the cache file and adds a row to XimNewsCache table.
	*  @param int IdNew
	*  @param int IdVersion
	*  @param int IdTemplate
	*  @param string xslBulletinFile
	*  @return bool
	*/

    function CreateCache($IdNew,$IdVersion,$IdTemplate,$xslBulletinFile){
		if (empty($IdNew)) {
		    $this->messages->add(_('News missing'), MSG_TYPE_ERROR);
		    //XMD_Log::info("Falta la noticia");
		    return false;
		}

		if (empty($IdVersion)) {
		    $this->messages->add(_('News version missing'), MSG_TYPE_ERROR);
		    XMD_Log::info(_("Version missing"));
		    return false;
		}

		if (empty($IdTemplate)) {
		    $this->messages->add(_('Bulletin pvd missing'), MSG_TYPE_ERROR);
		    XMD_Log::info(_("Pvd missing"));
		    return false;
		}


		$xml = $this->FilterXml($IdNew,$IdVersion,$xslBulletinFile);

		if(!$xml){
		    return false;
		}
		$filePath = Config::GetValue("AppRoot") . Config::GetValue("FileRoot"). "/";
		$fileName = FsUtils::getUniqueFile($filePath);
		$targetPath = $filePath . $fileName;
		
		
		if (FsUtils::file_put_contents($targetPath, $xml) === false) {
			$this->messages->add(_('Disk writing error'), MSG_TYPE_ERROR);
			return false;
		}

		$now = mktime();

		$this->set('IdNew', $IdNew);
		$this->set('IdTemplate', $IdTemplate);
		$this->set('IdVersion', $IdVersion);
		$this->set('File', $fileName);
		$this->set('Fecha', $now);
		$this->set('Counter', 0);
		$cache = parent::add();

		if($cache < 0){
		    XMD_Log::info("Inserting in database");
		    return false;
		}

		return $cache;
    }

	/**
	*  Checks if exist a row from XimNewsCache which matching the values of IdNew, IdVersion and IdTemplate.
	*  @param int IdNew
	*  @param int IdVersion
	*  @param int IdTemplate
	*  @return int|false
	*/

	function CheckExistence($IdNew,$IdVersion,$IdTemplate){
		$dbConn = new DB();
		$sql = "SELECT IdCache FROM XimNewsCache WHERE IdNew = $IdNew AND IdVersion = $IdVersion AND IdTemplate = $IdTemplate";
		$dbConn->Query($sql);

		if ($dbConn->numRows == 0){
			return false;
		}

		$IdCache = $dbConn->GetValue('IdCache');
		unset($dbConn);

		return $IdCache;
	}

	/**
	*  Builds the content of the cache file.
	*  @param int nodeId
	*  @param int versionId
	*  @param string xslBulletinFile
	*  @return string|null
	*/

    function FilterXml($nodeId, $versionId, $xslBulletinFile){

		$dataFactory = new DataFactory($nodeId);
        list($version,$subversion) = $dataFactory->GetVersionAndSubVersion($versionId);
        $newsXml = $dataFactory->GetContent($version, $subversion);

         if (is_null($newsXml) || ($newsXml == "")) {
            XMD_Log::error("Void XML news $nodeId");
            return NULL;
        }

		// Gets news data

		$doc = new DomDocument;
		$doc->validateOnParse = true;
		$newsXml = '<?xml version="1.0" encoding="' . Config::getValue('dataEncoding') . '"?>' . $newsXml;

		$doc->LoadXML(XmlBase::recodeSrc($newsXml, Config::getValue('dataEncoding')));

		$xpath = new DOMXPath($doc);
		$nodeList = $xpath->query('//*[@boletin = "yes"]');

		foreach ($nodeList as $domNode) {

			$type = $domNode->getAttribute('type');

			if (in_array($type, array('text', 'textarea'))) {
				
				$name = $domNode->getAttribute('name');

				if (!is_null($name)) {
					$newsData[$name] = $domNode->nodeValue;
				}
			
			} else if ($type = 'attribute') {

				if ($domNode->hasAttributes()) {

					foreach($domNode->attributes as $att) {

						$newsData[$att->nodeName] = $att->nodeValue;
					}

				}

			} else {
				// nothing to do
			}

		}

		if (is_null($newsData)) {
			XMD_Log::error("Getting data from news $nodeId");
			return NULL;
		}

		// Builds the cache
 	
        $xmlNew = XIMDEX_ROOT_PATH . Config::GetValue('TempRoot') . '/dummy.xml';		
		FSUtils::file_put_contents($xmlNew, '<?xml version="1.0" encoding="UTF-8"?><para>testing</para>');
		$xslBulletin = XIMDEX_ROOT_PATH . Config::GetValue('FileRoot') . '/' . $xslBulletinFile;

		$xsltHandler = new XSLT();
		$xsltHandler->setXML($xmlNew);
		$xsltHandler->setXSL($xslBulletin);

		foreach ($newsData as $param => $value) {
			$xsltHandler->setParameter(array($param => $value));
		}
		
		$cacheXml = $xsltHandler->process();

		if (!$cacheXml) {
			XMD_Log::error(_("Generating ximNEWSCache for node $nodeId"));
			return NULL;
		}

		$xsltHandler->__destruct();

		$pos = strpos($cacheXml, "?>");
		$cacheXml = substr($cacheXml, $pos + 2);

        return $cacheXml;
    }

	/**
	*  A wrapper for DeleteCache
	*/

    function delete() {
    	return $this->DeleteCache();
    }

	/**
	*  Deletes the cache file and the row in the table XimNewsCache.
	*  @return bool
	*/

	function DeleteCache(){
		// Deleting and unpublishing the node
		$sync = new SynchroFacade();
		$sync->deleteAllTasksByNode($this->get('IdNew'), true);

		// Borra la tabla cache
		$IdCache = $this->get('IdCache');
		$fileName = $this->get('File');
		$config = new Config();
		$targetPath = $config->GetValue("AppRoot") . $config->GetValue("FileRoot"). "/". $fileName;

		if(!FsUtils::delete($targetPath)){
			XMD_Log::info(_("Deleting file $targetPath"));
			return false;
		}

		$numRows = parent::delete();
		if($numRows == 0){
			XMD_Log::info(_("Deleting cache $IdCache"));
			return false;
		}

		return true;
	}

	/**
	*  Gets the content of the cache file
	*  @return string|false
	*/

    function getContentCache(){
		$cacheFile = $this->get('File');

		if(!$cacheFile){
		    XMD_Log::warning(sprintf(_('Empty file')));
		    return false;
		}

		$config = new Config();
		$targetPath = $config->GetValue("AppRoot") . $config->GetValue("FileRoot"). "/". $cacheFile;
		$content = file_get_contents($targetPath);

		return $content;
    }

	/**
	*  Gets the IdCache field of the row from XimNewsCache which matching the values of IdNew, IdVersion and IdTemplate.
	*  @param int idBulletin
	*  @return int|false
	*/

	function GetIdCache($IdNew,$IdVersion,$IdTemplate){
		$dbConn = new DB();
		$sql = "SELECT IdCache FROM XimNewsCache WHERE IdNew = $IdNew AND IdVersion = $IdVersion AND IdTemplate = $IdTemplate";
		$dbConn->Query($sql);

		if ($dbConn->numRows == 0){
			return false;
		}

		$counter = $dbConn->GetValue('IdCache');
		unset($dbConn);

		return $counter;
	}

	/**
	*  Updates the Counter field from XimNewsCache by substracting 1.
	*  @param int counter
	*  @return bool
	*/

	function RestCounter($counter){
		$this->set('Counter',$counter);
		$numRows = parent::update();

		if($numRows == 0){
			XMD_Log::warning(_("ERROR subtracting in counter  ").$this->get('IdCache'));
			return false;
		}

		//Si el contador se queda a cero elimino la caché
		if($this->get('Counter') == 0){
			$this->DeleteCache();
		}

		return true;
	}

	/**
	*  Generates all caches of the documents created by a pvd template.
	*  @param int IdTemplate
	*  @param string xslFile
	*  @return bool
	*/

    function GenerateAllCaches($IdTemplate,$xslFile) {
		$dbConn = new DB();
		$sql = "SELECT IdCache, IdNew, IdVersion, File FROM XimNewsCache WHERE IdTemplate = $IdTemplate";
		$dbConn->Query($sql);

		if ($dbConn->numRows == 0){
		    XMD_Log::info(_("This pvd has not caches"));
		    return false;
		}

		$caches = array();
		$i = 0;
		while (!$dbConn->EOF) {
		    $caches[$i]['id'] = $dbConn->GetValue('IdCache');
		    $caches[$i]['new'] = $dbConn->GetValue('IdNew');
		    $caches[$i]['version'] = $dbConn->GetValue('IdVersion');
		    $caches[$i]['filename'] = $dbConn->GetValue('File');
		    $i++;
		    $dbConn->Next();
		}

		unset($dbConn);

		foreach($caches as $n => $cache){
		    //Obtaining XML new file and replacing the previous one
		    $xml = $this->FilterXml($cache['new'],$cache['version'],$xslFile);
		    if(!$xml){
			XMD_Log::info(_("Wrong XML in new {$cache['new']} version {$cache['version']} pvd $IdTemplate"));
		    }

		    $fileName = $cache['filename'];
		    $config = new Config();
		    $targetPath = $config->GetValue("AppRoot") . $config->GetValue("FileRoot"). "/". $fileName;
		    if (FsUtils::file_put_contents($targetPath, $xml) === false) {
				$this->messages->add(_('Disk writing error (2)'), MSG_TYPE_ERROR);
				return false;
		    }

		    XMD_Log::info(_("Updating file $targetPath"));
		}

		return true;
    }

	/**
	*  Deletes the rows from XimNewsCache which matching the value of IdNew.
	*  @param int idNew
	*  @return bool
	*/

	function deleteByNew($idNew) {
		$dbObj = new DB();
		$sql = sprintf("DELETE FROM XimNewsCache WHERE IdNew = %s", $dbObj->sqlEscapeString($idNew));
		return $dbObj->Execute($sql);
	}
 	
	/**
	*  Gets the rows from XimNewsCache which matching the value of IdNew.
	*  @param int idNew
	*  @return bool
	*/

    function GetCachesFromNew($idNew){
        $dbConn->Query("SELECT IdCache FROM XimNewsCache WHERE IdNew = $idNew");

        if ($dbConn->numRows == 0){
            XMD_Log::info(_("This news has not caches"));
            return false;
        }

        $caches = array();
        while (!$dbConn->EOF) {
            $caches[] = $dbConn->GetValue('IdCache');
            $dbConn->Next();
        }

        return $caches;
    }

}
?>
