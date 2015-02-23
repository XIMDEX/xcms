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



if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../../../"));

include_once(XIMDEX_ROOT_PATH . '/inc/mail/Mail.class.php');

ModulesManager::file('ximSYNC', '/inc/manager/BatchManager.class.php');
ModulesManager::file('ximNEWS', '/inc/model/XimNewsNews.inc');
ModulesManager::file('ximSYNC', '/inc/model/NodesToPublish.class.php');
ModulesManager::file('ximSYNC', '/conf/synchro.conf');

/**
*	@brief Manages the phases previous and later of the publication process.
*/

class SyncManager {

	// State flags.
	var $workflow;
	var $deleteOld;
	var $markEnd;
	var $linked;
	var $type;
	var $bulletinID;
	var $mail;

	//flag for the add to colector otf publication
	var $otfPublication;

	private $docsToPublishByLevel = array();
	private $computedDocsToPublish = array();
	private $pendingDocsToPublish = array();

	function SyncManager() {

		// Default values for state flags.
		$this->setFlag('workflow', true);
		$this->setFlag('deleteOld', false);
		$this->setFlag('markEnd', false);
		$this->setFlag('linked', false);
		$this->setFlag('recurrence', false);
		$this->setFlag('type', 'core');
		$this->setFlag('bulletinID', NULL);
		$this->setFlag('mail', false);
		$this->setFlag('otfPublication',false);

		$this->setFlag('deeplevel', DEEP_LEVEL < 0 ? 1 : DEEP_LEVEL);
		$this->setFlag('globalForcePublication', FORCE_PUBLICATION);
	}

	/**
	*  Sets the value of any variable.
	*  @param string key
	*  @param unknown value
	*/

	function setFlag($key, $value) {

		$this->$key = $value;
	}

	/**
	*  Gets the value of any variable.
	*  @param string key
	*  @return unknown
	*/

	function getFlag($key) {
		if (isset($this->$key)) {
			return $this->$key;
		}
		return NULL;
	}

	/**
	 * Function for callback in MPM.
	 * @param array args
	 * @return array
	 */

	function pushDocInPublishingPoolForMPM($args){
		$nodeID = $args[0];
		$colectorID=$args[1];
		$up = $args[2];
		$down=null;

		$this->setFlag('type', 'ximNEWS');
		$this->setFlag('colector', $colectorID);

		$ret = $this->pushDocInPublishingPool($nodeID,$up,$down);

		return $ret;
	}

	public function buildPublishingDependencies($idNode, $params){
		$this->pendingDocsToPublish = $this->computedDocsToPublish = array();
		$this->pendingDocsToPublish[] = $idNode;
		$this->docsToPublishByLevel["$idNode"] = 0;

		while(!empty( $this->pendingDocsToPublish )){
			$nodeId = array_shift( $this->pendingDocsToPublish );
			if (  $this->hasDependences($nodeId, $params)){
				continue;
			}
			$this->computedDocsToPublish[] = $nodeId;
		}

		return $this->computedDocsToPublish;
	}

	/**
	*  Gets the Nodes that must be published with the current Node and calls the methods for build the Batchs.
	*  @param int $nodeId
	*  @param int up
	*  @param int down
	*  @return array|null
	*/

	function pushDocInPublishingPool($idNode, $up, $down = null) {
		if (is_null($idNode)) {
			XMD_Log::error(_("Pushdocinpool - Empty IdNode"));
			return NULL;
		}

		$force = $this->getFlag('globalForcePublication')?true:$this->getFlag("force");
		$params['deeplevel'] = $this->getFlag('deeplevel');
		$lastPublishedDocument = $this->getFlag("lastPublished");
		// flags for dependencies
		$params['withstructure'] = ($this->getFlag('structure') === false)? false : true;
		$node = new Node($idNode);
		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error(sprintf(_("Node %s does not exist") , $idNode) );
			return NULL;
		}


		$docsToPublish = $this->buildPublishingDependencies($idNode, $params);

		if ($node->nodeType->get('IsPublicable') == '1') {

			if (sizeof($docsToPublish) > 0) {
				$docsToPublish = array_unique(array_merge(array($idNode), $docsToPublish));
			} else {
				$docsToPublish = array($idNode);
			}
		}else {
			return array();
		}


		$userID = \Ximdex\Utils\Session::get('userID');
		foreach ($docsToPublish as $idDoc) {
			if (!array_key_exists($idDoc, $this->docsToPublishByLevel)){
				continue;
			}
			$deepLevel = $this->docsToPublishByLevel[$idDoc];
			$ntp = NodesToPublish::create($idDoc, $idNode, $up, $down, $userID, $force, $lastPublishedDocument, $deepLevel);

		}


		if($this->getFlag('mail')){
			$this->sendMail($idNode, $type, $up, $down);
		}

		// Exec batchmanagerdaemon in background and get its pid (in case we needed in the future)
		$cmd = 'php ' . XIMDEX_ROOT_PATH . '/modules/ximSYNC/inc/manager/BatchManagerDaemon.php';
		$pid = shell_exec(sprintf("%s > /dev/null & echo $!", $cmd));

		return $docsToPublish;
	}


	/**
	 * @param $nodeId
	 * @param $params
	 * @param $currentDeepLevel
	 * @return mixed
	 */
	public function hasDependences($nodeId, $params){

		$deepLevel = $params["deeplevel"];
		if (!isset($this->docsToPublishByLevel["$nodeId"])){
			return false;
		}

		$currentDeepLevel = $this->docsToPublishByLevel["$nodeId"] + 1;
		if ($deepLevel != -1 && $deepLevel < $currentDeepLevel){
			return false;
		}

		$node = new Node($nodeId);
		$nodeDependences = $node->class->getPublishabledDeps($params);

        if (!isset( $nodeDependences ) || empty($nodeDependences)) {
            return false  ;
        }
         $pending = array_values( array_diff( $nodeDependences, $this->pendingDocsToPublish, $this->computedDocsToPublish  ) ) ;
         if ( empty( $pending )) {
            return false ;
        } else {
			 $idDoc = $pending[0];
			 $this->docsToPublishByLevel["$idDoc"]=$currentDeepLevel;
             $this->pendingDocsToPublish =array_merge(  [ $idDoc , $nodeId  ]   ,  $this->pendingDocsToPublish  )  ;
            return true ;
        }
	}

	function sendMail($nodeID, $type, $up, $down) {
		$node = new node($nodeID);
		$name = $node->Get('Name');

		if(strtolower($type) == 'ximnews'){
			$bulletinID = $this->getFlag('bulletinID');
			$node = new node($bulletinID);
			$bulletinName = $node->Get('Name');
			$msg = sprintf(_("News %s is going to be published in the bulletin %s"), $name, $bulletinName);
		}
		else{
			$msg = sprintf(_("Node %s is going to be published"), $name);
		}

		if(!$down){
			$downString = _('Undetermined');
		}
		else{
			$downString = date('d-m-Y H:i:s',$down);
		}

		$msg .= "\n"._("Publication date").": ".date('d-m-Y H:i:s',$up);
		$msg .= "\n"._("Expiration date").":"." $downString";

		$user = new User(301);
		$email = $user->Get('Email');
		$mail = new Mail();
		$mail->addAddress($email);
		$mail->Subject = _("Publication of")." $name";
		$mail->Body = $msg;
		$mail->Send();
	}
}

?>