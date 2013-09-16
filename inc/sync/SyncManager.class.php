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




/**
 * XIMDEX_ROOT_PATH
 */
if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . "/../../"));

include_once( XIMDEX_ROOT_PATH . '/inc/mail/Mail.class.php');

/**
 *
 */
class SyncManager {

	// member vars.
	var $errors;
	var $errors_str;

	// State flags.
	var $workflow;
	var $deleteOld;
	var $markEnd;
	var $linked;
	var $type;
	var $bulletinID;
	var $mail;
	var $recurrence;


	function SyncManager() {

		// Default values for state flags.
		$this->setFlag('workflow', true);
		$this->setFlag('deleteOld', false);
		$this->setFlag('markEnd', false);
		$this->setFlag('linked', false);
		$this->setFlag('type', 'core');
		$this->setFlag('bulletinID', NULL);
		$this->setFlag('mail', false);
		$this->setFlag('recurrence', false);
	}

	function setFlag($key, $value) {

		$this->$key = $value;
	}

	function getFlag($key) {

		return $this->$key;
	}

	// TODO: should return sync hash.
	function pushDocInPublishingPool($node_id, $up, $down, $recurrence = false) {

		// Acquire state flags
		$markEnd = $this->getFlag('markEnd');
		$workflow = $this->getFlag('workflow');
		$linked = $this->getFlag('linked');
		$deleteOld = $this->getFlag('deleteOld');
		$type = strtolower($this->getFlag('type'));

		$sync = new Synchronizer($node_id);

		// Delete frame if necessary
		if ( $deleteOld ) {
			// DUE -> OUTDATED older frames.
			$sync->DeleteFlaps($node_id, $up, $down, $markEnd);
			// Frame adjusting.
			// $sync->DeleteFramesFromNow($node_id,1,$up);
		}

		// TODO: Resolve linked question...
		if ( $linked ) {
			$link_level = 0;
		} else {
			$link_level = 1;
		}

		// Increment major version.
		$data = new DataFactory($node_id);
		$versionID = $data->AddVersion(true);

		// Create frame.
		$frameID = $sync->CreateFrame($up, $down, $markEnd, $link_level, $workflow);

		// Error handling
		if ( $sync->numErr ) {
			$this->errors = true;
			$this->errors_str = $sync->msgErr;

			
			// Delete major version previously createds

			$data->DeleteVersion($versionID);

			// exist with errors.
			return NULL;
		} else {
			$this->errors = false;
			$this->errors_str = NULL;
		}

		$node = new Node($node_id);
		if ($node->nodeType->get('Module') == 'ximNEWS') {
//		if (strtolower($type) == 'ximnews') {
			$sync->AssociateFrameVersion($frameID[0],$versionID);
		}

		// Add relation to cache.
		if (Config::getValue('dexCache')) {
			DexCache::setRelation($node_id, $frameID, $versionID);
		}

		// Send mail
		if($this->getFlag('mail')){
		    $node = new node($node_id);
		    $name = $node->Get('Name');

			if ($node->nodeType->get('Module') == 'ximNEWS') {
	//	    if(strtolower($type) == 'ximnews'){
				$bulletinID = $this->getFlag('bulletinID');
				$node = new node($bulletinID);
				$bulletinName = $node->Get('Name');
				$msg = "La noticia $name se va a publicar en el boletin $bulletinName";
		    }
		    else{
				$msg = "El nodo $name se va a publicar";
		    }

		    if(!$down){
				$downString = 'Sin determinar';
		    }
		    else{
				$downString = date('d-m-Y H:i:s',$down);
		    }

		    $msg .= "\nFecha de publicacion: ".date('d-m-Y H:i:s',$up);
		    $msg .= "\nFecha de despublicacion: $downString";

		    $user = new User(301);
			$email = $user->Get('Email');
			$mail = new Mail();
			$mail->addAddress($email);
			$mail->Subject = "Publicacion de $name";
			$mail->Body = $msg;
			$mail->Send();
		}

	}

	function error() {

		return $this->errors;
	}

	function errorToString() {

		return $this->errors_str;
	}

}

?>
