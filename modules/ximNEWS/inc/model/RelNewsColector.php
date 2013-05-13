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




ModulesManager::file('/inc/model/orm/RelNewsColector_ORM.class.php', 'ximNEWS');
ModulesManager::file('/inc/persistence/Config.class.php');


class RelNewsColector extends RelNewsColector_ORM  {
	
	/**
	*  Sets the field FechaOut to current date to the rows from RelNewsColector which matching the value of IdNew.
	*  @param int idNew
	*  @return int
	*/

	function deleteByNew($idNew) {
		$dbObj = new DB();
		$query = sprintf("UPDATE RelNewsColector SET FechaOut = ".mktime()." WHERE IdNew = %s", 
			$dbObj->sqlEscapeString($idNew));
		return $dbObj->Execute($query);
	}

	/**
	*  Checks if exist a row from RelNewsColector which matching the values of IdColector and IdNew.
	*  @param int idColector
	*  @param int idNews
	*  @return bool
	*/

	function hasNews($idColector,$idNews){
		$dbObj = new DB();
		$query = sprintf("SELECT IdRel FROM RelNewsColector WHERE IdColector= %s AND IdNew = %s",
				$dbObj->sqlEscapeString($idColector),
				$dbObj->sqlEscapeString($idNews));
		$dbObj->Query($query);
	
		return $dbObj->numRows > 0 ? $dbObj->GetValue('IdRel') : false;
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdNews.
	*  @param int idNews
	*  @return array
	*/

	function getColectorsFromNew($idNews) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdColector FROM RelNewsColector WHERE IdNew = %s ORDER BY IdColector ASC",
				$dbObj->sqlEscapeString($idNews));
		$dbObj->Query($query);
		$result = array();
		while (!$dbObj->EOF) {
			$result[] = $dbObj->GetValue('IdColector');
			$dbObj->Next();
		}		 
		return $result;
	}

	/**
	*  Gets the rows from RelNewsColector which matching the value of IdNews.
	*  @param int idNews
	*  @return array
	*/

	function getAllColectorsFromNew($idNews) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdColector,State,FechaIn,FechaOut,Version,Subversion FROM RelNewsColector WHERE IdNew = %s ORDER BY IdColector ASC",
				$dbObj->sqlEscapeString($idNews));
		$dbObj->Query($query);
		$result = array();

	    	while (!$dbObj->EOF) {
			$result[] = array( 'IdColector' => $dbObj->GetValue('IdColector'), 
					'State' => $dbObj->GetValue('State'),
					'FechaIn' => $dbObj->GetValue('FechaIn'),
					'FechaOut' => $dbObj->GetValue('FechaOut'),
					'Version' => $dbObj->GetValue('Version'),
					'Subversion' => $dbObj->GetValue('SubVersion'));
			$dbObj->Next();		
	    	}
	
		return $result;
	}

	/**
	*  Gets the number of rows from RelNewsColector which matching the value of IdNews and langId.
	*  @param int colectorID
	*  @param int langId
	*  @return int
	*/

	function countNews($colectorID,$langId) {
		$dbObj = new DB();
		$query = sprintf("SELECT COUNT(IdNew) as NewsCount FROM RelNewsColector"
					. " WHERE IdColector = %s AND LangId = %s AND (State='publishable' OR State='InBulletin')",
					$dbObj->sqlEscapeString($colectorID),
					$dbObj->sqlEscapeString($langId));
		
		$dbObj->Query($query);
		return $dbObj->GetValue('NewsCount');
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdColector.
	*  @param int colectorID
	*  @return array
	*/

	function getNews($colectorID) {
		$dbObj = new DB();
		$query = sprintf("SELECT RelNewsColector.IdNew FROM RelNewsColector INNER JOIN XimNewsNews ON 
					(RelNewsColector.IdNew = XimNewsNews.IdNew AND RelNewsColector.IdColector = %s) 
					ORDER BY XimNewsNews.Name ASC",
					$dbObj->sqlEscapeString($colectorID));
		$dbObj->Query($query);
		
		$result = array();
		while (!$dbObj->EOF) {
			$result[] = $dbObj->GetValue('IdNew');
			$dbObj->Next();
		}		 
		return $result;
	}
	
	/**
	*  Gets the row from RelNewsColector which matching the values of IdNew and IdColector.
	*  @param int colectorID
	*  @param int newID
	*  @return  int|false
	*/

	function GetRelation($colectorID,$newID){
		$dbObj = new DB();
		$query = sprintf("SELECT IdRel FROM RelNewsColector WHERE IdColector = %s  AND IdNew = %s AND State != 'removed'",
				$dbObj->sqlEscapeString($colectorID),
				$dbObj->sqlEscapeString($newID));
				
	    $dbObj->Query($query);
	
		return $dbObj->numRows > 0 ? $dbObj->GetValue('IdRel') : false;
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of State.
	*  @param string status
	*  @return  array
	*/

	function search($status) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdNew, IdCache FROM RelNewsColector WHERE State = %s", 
				$dbObj->sqlEscapeString($status));
		$dbObj->Query($query);
		
		$result = array();
		while (!$dbObj->EOF) {
			$result[]['cache'] = $dbObj->GetValue('IdCache');
			$result[]['new'] = $dbObj->GetValue('IdNew');
			$dbObj->Next();		
		}
	
		return $result;
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdColector.
	*  @param int idColector
	*  @return  array
	*/

	function getAddedNews($idColector){
		$dbObj = new DB();
		$query = sprintf("SELECT IdNew,State,FechaIn,FechaOut,Version,Subversion FROM RelNewsColector WHERE IdColector = %s",$dbObj->sqlEscapeString($idColector));
	    	$dbObj->Query($query);
	    
	    	$result = array();

		if (!($dbObj->numRows > 0)) {			
			XMD_Log::info("Colector $idColector void");
			return NULL;
		}

	    	while (!$dbObj->EOF) {
			$result[] = array( 'IdNew' => $dbObj->GetValue('IdNew'), 
					'State' => $dbObj->GetValue('State'),
					'FechaIn' => $dbObj->GetValue('FechaIn'),
					'FechaOut' => $dbObj->GetValue('FechaOut'),
					'Version' => $dbObj->GetValue('Version'),
					'Subversion' => $dbObj->GetValue('SubVersion'));
			$dbObj->Next();		
	    	}
	
	    	return $result;
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdColector.
	*  @param int idColector
	*  @return  array
	*/

	function getNewsFromColector($idColector){
		$dbObj = new DB();
		$query = sprintf("SELECT IdNew FROM RelNewsColector WHERE IdColector = %s",$dbObj->sqlEscapeString($idColector));
	    	XMD_Log::info($query);
	    	$dbObj->Query($query);
	    
	    	$result = array();

		if (!($dbObj->numRows > 0)) {			
			XMD_Log::info(sprintf(_("Colector %s void"), $idColector));
			return NULL;
		}

	    	while (!$dbObj->EOF) {
			$result[] = $dbObj->GetValue('IdNew');
			$dbObj->Next();		
	    	}
	
	    	return $result;
	}
	
	/**
	*  Sets the field State to Publishable to the rows from RelNewsColector which matching the value of a timestamp.
	*  @param int timeStamp
	*  @return int
	*/

	function setPublishNews($timeStamp){
		$dbObj = new DB();

		$dbObj->Execute("UPDATE RelNewsColector SET State='publishable' WHERE FechaIn <= $timeStamp AND State = 'pending'");

		XMD_Log::info(sprintf(_("Update %s pending to publishable from relNewsColectors"), $dbObj->numRows));
		return $dbObj->numRows;
	}

	/**
	*  Sets the field State to Removed to the rows from RelNewsColector which matching the value of a timestamp.
	*  @param int timeStamp
	*  @return int
	*/

	function getOutdatedNews($timeStamp){
		$dbObj = new DB();
		$sql = "UPDATE RelNewsColector SET State='removed' 
			WHERE FechaOut < $timeStamp AND FechaOut IS NOT NULL AND State!='removed'";
		$dbObj->Execute($sql);

		XMD_Log::info(sprintf(_("Update %s states to removed from relNewsColectors"), $dbObj->numRows));
		return $dbObj->numRows;
	}

	/**
	*  Deletes a row from RelNewsColector and its associated XimNewsCache.
	*  @return bool
	*/

	function delete() {
	    $cache = new XimNewsCache($this->get('IdCache'));
	    $counter = $cache->get('Counter') - 1;
	    $cache->RestCounter($counter);
	
	    return parent::delete();
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdColector and its State is Removed.
	*  @param int idColector
	*  @return  array
	*/

	function getRemoved($idColector){ 
		$dbObj = new DB();
		$query = "SELECT IdNew, IdCache FROM RelNewsColector WHERE State = 'removed' AND IdColector = ".$dbObj->sqlEscapeString($idColector).""; 
		
		$dbObj->Query($query);
		$caches = array(); 
		$i = 0;

		while (!$dbObj->EOF) { 
			$caches[$i]['cache'] = $dbObj->GetValue('IdCache'); 
			$caches[$i]['new'] = $dbObj->GetValue('IdNew'); 
			$i++;
			$dbObj->Next();            
		} 
		
		return $caches; 
	}
	
	/**
	*  Deletes the rows from RelNewsColector which matching the value of IdColector and its State is Removed.
	*  @param int idColector
	*  @return int
	*/

	function purgeColector($idColector) {
	    $dbObj = new DB();
	    $query = sprintf("DELETE FROM RelNewsColector WHERE State='removed' AND IdColector = %s",
	    		$dbObj->sqlEscapeString($idColector));
		$dbObj->Execute($query);
	}
	
	/**
	*  Updates the fields PosInSet, Page and State to an array of news.
	*  @param int idColector
	*  @param array arrayNews
	*  @return int
	*/

	function updatePosInSet($idColector, $arrayNews){
		$news = implode(',', $arrayNews);

		$dbObj = new DB();
	    $query = "UPDATE RelNewsColector SET PosInSet = PosInSet2, Page = Page2, State='InBulletin' 
			WHERE IdColector = $idColector AND IdNew in ($news)";

	    return $dbObj->Execute($query);
	}

	/**
	*  Updates the field State to Removed for the rows from RelNewsColector which matching the value of IdColector.
	*  @param int idColector
	*  @return int
	*/

	function setColectorRemoved($idColector) {
		$dbObj = new DB();
		$query = sprintf("UPDATE RelNewsColector SET State = 'removed' WHERE IdColector = %s",
				$dbObj->sqlEscapeString($idColector));
		return $dbObj->Execute($query);
		
	}

	/**
	*  Updates the fields PosInSet2 and Page2 Sate by pagination criteria.
	*  @param int idColector
	*/

	function sortNewsInSetByDate($colectorID, $set, $newsPerBulletin, $order, $langID, $sinFuelle = false) {
		$dbObj = new DB();
		$query = sprintf("SELECT RelNewsColector.IdRel FROM RelNewsColector, XimNewsNews"
				. " WHERE RelNewsColector.IdColector = %s AND RelNewsColector.SetAsoc = %s"
				. " AND RelNewsColector.LangId = %s AND RelNewsColector.State NOT IN ('pending', 'removed')"
				. " AND RelNewsColector.IdNew = XimNewsNews.IdNew"
				. " ORDER BY XimNewsNews.Fecha %s, XimNewsNews.TimeStamp %s, XimNewsNews.IdNew %s",
				$dbObj->sqlEscapeString($colectorID),
				$dbObj->sqlEscapeString($set),
				$dbObj->sqlEscapeString($langID),
				$order, $order, $order);

		$dbObj->Query($query);

		$rels = array();

		while (!$dbObj->EOF) {
			$rels[] = $dbObj->GetValue('IdRel');
			$dbObj->Next();		
		}
		
		// Bulletin which were modified

		if ($sinFuelle == true) {
			$bulletins = $this->sinFuelle(sizeof($rels), $newsPerBulletin); 
		} else {
			$bulletins = $this->fuelle(sizeof($rels), $newsPerBulletin); 
		}

		$j = 0;

		foreach ($bulletins as $bulletin => $numNews) {
			$posInPage = 0;

			for ($i = 0; $i < $numNews; $i++) {
				$relNewsColector = new RelNewsColector($rels[$j]);
				$relNewsColector->set('PosInSet2', $posInPage);
				$relNewsColector->set('Page2', $bulletin + 1);
				$relNewsColector->update();

				$j++;
				$posInPage++;
			}
		}
				 	
	}

	/**
	*  A wrapper for the method set.
	*  @param string attribute
	*  @param string value
	*  @return bool
	*/

	function set($attribute, $value) {
		return parent::set($attribute, $value);
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdColector and its State is Removed or Publishable.
	*  @param int idColector
	*  @return  array
	*/

	function getPublishNews($idColector) {
		$dbObj = new DB();
		$query = sprintf("SELECT IdNew FROM RelNewsColector"
				. " WHERE IdColector = %s AND (State='publishable' OR State='removed')",
				$dbObj->sqlEscapeString($idColector));
	
		$dbObj->Query($query);
	
		$resultado = array();
		while (!$dbObj->EOF) {
			$resultado[] = $dbObj->GetValue('IdNew');
			$dbObj->Next();
		}
	
		return $resultado;
	}
	
	/**
	*  Gets all the sets of a given Colector.
	*  @param int idColector
	*  @return  array
	*/

	function getSetsFromColector($idColector) {	
		$dbObj = new DB();
		$query = sprintf("SELECT DISTINCT(SetAsoc) AS SetAsoc"
				. " FROM RelNewsColector WHERE IdColector = %s AND State != 'Pending'",
				$dbObj->sqlEscapeString($idColector));

		$dbObj->Query($query);

		$resultado = array();
		while (!$dbObj->EOF) {
			$resultado[] = $dbObj->GetValue('SetAsoc');
			$dbObj->Next();
		}		 

		return $resultado;
	}

	/**
	*  Gets all the sets of a given Colector, Set and Lang.
	*  @param int colectorID
	*  @param string set
	*  @param int langID
	*  @return  array
	*/

	function getPages($colectorID, $set, $langID, $newsPerBulletin, $total=NULL){
		$dbObj = new DB();
		if($total){
			$query = sprintf("SELECT Distinct(Page2) AS Page2"
					. " FROM RelNewsColector"
					. " WHERE LangId = %s"
					. " AND IdColector = %s"
					. " AND SetAsoc = %s"
					. " AND State!='removed'"
					. " AND Page2 IS NOT NULL"
					. " ORDER BY Page2 ASC",
					$dbObj->sqlEscapeString($langID),
					$dbObj->sqlEscapeString($colectorID),
					$dbObj->sqlEscapeString($set));
		} else {
			$query = sprintf("SELECT Distinct(Page2) AS Page2"
					. " FROM RelNewsColector"
					. " WHERE LangId = %s"
					. " AND IdColector = %s"
					. " AND SetAsoc = %s"
					. " AND ( PosInSet != PosInSet2	OR PosInSet IS NULL OR State='removed' )"
					. " AND Page2 IS NOT NULL"
					. " ORDER BY Page2 ASC",
					$dbObj->sqlEscapeString($langID),
					$dbObj->sqlEscapeString($colectorID),
					$dbObj->sqlEscapeString($set));
		}
		$dbObj->Query($query);

		$resultado = array();
		while (!$dbObj->EOF) {
			$resultado[] = $dbObj->GetValue('Page2');
			$dbObj->Next();
		}
		return (!empty($resultado)) ? $resultado : false;
	}

	/**
	*  Gets all the news of a given Colector, Set, Page and Lang.
	*  @param int colectorID
	*  @param string set
	*  @param array array_pages
	*  @param int langID
	*  @return  array
	*/

	function getNewsFromPages($colectorID, $set, $array_pages, $langID){	
		$dbObj = new DB();

		if (is_array($array_pages)) {
			$sqlArrayPages = implode(", ",$array_pages);
		} else {
			$sqlArrayPages = $array_pages;
		}

		$query = sprintf("SELECT IdNew FROM RelNewsColector"
				. " WHERE LangId = %s"
				. " AND IdColector = %s"
				. " AND SetAsoc = %s"
				. " AND Page2 IN (%s)"
				. " AND State in ('publishable', 'InBulletin')"
				. " ORDER BY Page2 ASC, PosInSet2 ASC",
				$dbObj->sqlEscapeString($langID),
				$dbObj->sqlEscapeString($colectorID),
				$dbObj->sqlEscapeString($set),
				$sqlArrayPages);
		$dbObj->Query($query);

		$resultado = array();
		while (!$dbObj->EOF) {
			$resultado[] = $dbObj->GetValue('IdNew');
			$dbObj->Next();
		}

		return $resultado;
	}

	/**
	*  Gets the highest page of a given Colector, Set and Lang.
	*  @param int idColector
	*  @param string set
	*  @param int idLanguage
	*  @return  int
	*/

	function GetMaxPage($idColector, $set, $idLanguage){
		$dbObj = new DB();
		
		$query = sprintf("Select MAX(Page2) AS Page2"
				. " FROM RelNewsColector"
				. " WHERE LangId = %s"
				. " AND IdColector = %s"
				. " AND SetAsoc = %s"
				. " AND State != 'removed'"
				. " AND Page2 IS NOT NULL",
				$dbObj->sqlEscapeString($idLanguage),
				$dbObj->sqlEscapeString($idColector),
				$dbObj->sqlEscapeString($set));
	
	    $dbObj->Query($query);
	
	    if(!($dbObj->numRows > 0)) {
			XMD_Log::info(_("Obtaining maxPage for colector").$idColector);
			return 0;
	    }
	
	    $max = $dbObj->GetValue('Page2');
	
	    if(is_null($max)){
			return 0;
	    }
	
	    return $max;
	}

	/**
	*  Gets the version from RelNewsColector which matching the value of IdColector and IdNew.
	*  @param int idColector
	*  @param int idNews
	*  @return  array
	*/

	function getNewsVersionInColector($idColector,$idNews) {
		$dbObj = new DB();
		$query = sprintf("SELECT Version, SubVersion FROM RelNewsColector WHERE IdColector= %s AND IdNew = %s",
				$dbObj->sqlEscapeString($idColector),
				$dbObj->sqlEscapeString($idNews));
		$dbObj->Query($query);
	
		if ($dbObj->numRows > 0) {
		 	$version = $dbObj->GetValue('Version');
			$subVersion = $dbObj->GetValue('SubVersion');

			return array($version, $subVersion);
		}
		
		return NULL;
	}

	/**
	*  Optimizes the pagination of news according to Fuelle algoritm.
	*  @param int totalNews
	*  @param int newsPerBulletin
	*  @return array
	*/

	function fuelle($totalNews, $newsPerBulletin) {
		// Quantity of news with can be over the number of news per bulletin in the bellows bulletins
		$exceededNews = floor($newsPerBulletin / Config::getValue('RatioNewsFuelle'));

		// Porcent of bellows bulletins
		$tolerance = Config::getValue('ToleranciaFuelle') / 100;

		// Each element of this array is a bulletin and its value is the number of news that bulletin has
		$bulletins = array();

		// Number of bulletins which will have bellows
		$numBulletinsFuelle = ceil($totalNews * $tolerance / $newsPerBulletin);

		// Number of news needed to re-page the whole colector
		// (bellows bulletins are full)
		$totalGenerationLimit = $exceededNews * $numBulletinsFuelle + 1;

		$alpha = ($totalNews - $numBulletinsFuelle * $newsPerBulletin) / $totalGenerationLimit;

		if ($alpha < 1) {
			$alpha = 0;
		} else {
			$alpha = floor($alpha);
		}

		// News which do not appear in bellows bulletins
		$newsOutBulletinsFuelle = $alpha * $totalGenerationLimit;

		// News in bellows bulletins
		$newsInBulletinsFuelle = $totalNews - $newsOutBulletinsFuelle;

		// Paging the bellows bulletins
		if ($totalNews <= $newsPerBulletin) {
			$bulletins[] = $totalNews;

		} else {
			// Left news once bellows is included
			$excedenteNoticias = $newsInBulletinsFuelle - $numBulletinsFuelle * $newsPerBulletin;

			if ($excedenteNoticias == 0) {
				for ($i = 0; $i < $numBulletinsFuelle; $i++) {
					$bulletins[] = $newsPerBulletin;
				}
			} else {
				$boletinesAbsorbentes = ceil($excedenteNoticias / $exceededNews);

				$newsOutBulletinsFuelleBoletines = $numBulletinsFuelle - $boletinesAbsorbentes;

				for ($i = 0; $i < $boletinesAbsorbentes; $i++) {
					$aa = $excedenteNoticias - $i * $exceededNews;
					$bb = $aa % $exceededNews;

					if ($i == 0) {
						if ($bb == 0) {
							$cc = $newsPerBulletin + $exceededNews;
						} else {
							$cc = $newsPerBulletin + $bb;
						}

					} else {
						$cc = $newsPerBulletin + $exceededNews;
					}

					$bulletins[] = $cc;
				}

				if ($newsOutBulletinsFuelleBoletines > 0 && $excedenteNoticias != 0) {
					for ($i = 0; $i < $newsOutBulletinsFuelleBoletines; $i++) {
						$bulletins[] = $newsPerBulletin;
					}
				}

			}

		}

		// Paging bulletins without bellows
		$bullsSinFuelle = floor($newsOutBulletinsFuelle / $newsPerBulletin);

		$newsInLastBulletin = $newsOutBulletinsFuelle % $newsPerBulletin;

		for ($i = 0; $i < $bullsSinFuelle; $i++) {
			$bulletins[] = $newsPerBulletin;
		}
		
		if ($newsInLastBulletin != 0) {
			$bulletins[] = $newsInLastBulletin;
		}

		return $bulletins;
	}

	/**
	*  Makes the pagination of news avoiding the Fuelle algoritm.
	*  @param int totalNews
	*  @param int newsPerBulletin
	*  @return array
	*/

	function sinFuelle($totalNews, $newsPerBulletin) {
		$numBulletins = $totalNews / $newsPerBulletin;

		for ($i = 1; $i < $numBulletins; $i++) {
			$bulletins[] = $newsPerBulletin;
		}

		$remainder = $totalNews % $newsPerBulletin;

		if ($remainder > 0) {
			$bulletins[] = $remainder;
		}

		return $bulletins;
	}

	/**
	*  Gets the colectors that must be paginated with the Fuelle algoritm.
	*  @return array
	*/

	function colectoresConFuelle() {
		$dbObj = new DB();

		$sql = "SELECT MAX(RelNewsColector.PosInSet) AS NumNews, RelNewsColector.IdColector, XimNewsColector.NewsPerBulletin
				FROM RelNewsColector, XimNewsColector WHERE RelNewsColector.Page=1 
				AND XimNewsColector.IdColector = RelNewsColector.IdColector 
			GROUP BY RelNewsColector.IdColector HAVING NumNews > NewsPerBulletin";

		$dbObj->Query($sql);

		$colectors = array();

		while (!$dbObj->EOF) {
			$colectors[] = $dbObj->GetValue('IdColector');
			$dbObj->Next();
		}

		return $colectors;
	}
	
	/**
	*  Updates the field FechaOut.
	*  @param int dateOut
	*  @return bool
	*/

	function setExpirationDate($dateOut) {
		if(!($this->get('IdRel') > 0))
			return false;
		
		$this->set('FechaOut', $dateOut);
		$this->update();
		return true;
	}
	
	/**
	*  Gets the rows from RelNewsColector which matching the value of IdNew and they aren't be published.
	*  @param int idNew
	*  @return array|null
	*/

	function getPendingRelationsByNew($idNew) {

		//TODO: What to do with 'removed' and 'publishable' states?
		//		They're post-automatic states but... It's necessary to notify user about them?
		
		if(is_null($idNew) || !($idNew > 0)) {
			XMD_Log::error(_('Cannot get Pending relations. IdNew is null or not positive integer.'));
			return NULL;
		}
		
		$relations = $this->find('IdRel, IdColector', 'IdNew = %s AND (State = %s OR FechaOut IS NOT NULL)', array($idNew, 'pending'), MULTI);

		if (!(sizeof($relations) > 0)) {
			XMD_Log::info(_('No pending relations found for new ') . $idNew);
			return NULL;
		}

		return $relations;
	}
	
	/**
	*  Gets the rows from RelNewsColector join XimNewsNews which matching the value of IdColector.
	*  @param int colectorID
	*  @param int from
	*  @param int range
	*  @return array
	*/

	function getAllNewsFromColector($colectorID, $from = 0, $range = 10) {
		$dbObj = new DB();
		$query = sprintf("SELECT XimNewsNews.Name, RelNewsColector.IdNew, RelNewsColector.State, RelNewsColector.FechaIn, RelNewsColector.FechaOut " .
				"FROM RelNewsColector INNER JOIN XimNewsNews ON " . 
				"(RelNewsColector.IdNew = XimNewsNews.IdNew AND RelNewsColector.IdColector = %s) " . 
				"ORDER BY XimNewsNews.Name ASC LIMIT %s,%s",
				$dbObj->sqlEscapeString($colectorID), $from, $range);
		$dbObj->Query($query);
		
		$result = array();
		while (!$dbObj->EOF) {
			$result[$dbObj->GetValue('IdNew')] = array(
				'State' => $dbObj->GetValue('State'),
				'FechaIn' => $dbObj->GetValue('FechaIn'),
				'FechaOut' => $dbObj->GetValue('FechaOut'),
				'Name' => $dbObj->GetValue('Name')
			);
			$dbObj->Next();
		}		 
		return $result;
	}
}
?>
