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



ModulesManager::file('/inc/utils.inc');


function baseIO_GetNextAllowedState($nodeID, $userID, $groupID){
	$user = new User($userID);
	$roleID = $user->GetRoleOnNode($nodeID, $groupID);
	$node = new Node($nodeID);
	$stateID=$node->GetState();
	$workflow = new Workflow($nodeID);
	$workflow->SetID($stateID);
	$allowedStates = $workflow->GetAllowedStates($roleID);
	if($allowedStates)
		do
			{
			$stateID = $workflow->GetNextState();
			$workflow->SetID($stateID);
			}
		while(!$workflow->IsFinalState() and !(is_array($$allowedStates) && in_array($stateID,$allowedStates)));

	if (is_array($allowedStates) && in_array($stateID, $allowedStates))
		  return $stateID;
	else
		  return null;
}
function Publicar($nodeID, $userID){
	$sync = new Synchronizer($nodeID);
	$lastFrame = $sync->GetLastFrameBulletin();
	$lastTime = $sync->GetDateDownOnFrame($lastFrame);
	if($lastFrame && !$lastTime) {
		$gaps = $sync->GetGapsBetweenDates(time(), $sync->GetDateUpOnFrame($lastFrame));
	} else {
		$gaps = $sync->GetGapsBetweenDates(time(), $lastTime);
		if($lastTime) {
			$lastGap = array($lastTime, null, null);
		} else {
			$lastGap = array(time(), null, null);
		}
		array_push($gaps,$lastGap);
	}
	setlocale (LC_TIME, "es_ES");
	if(sizeof($gaps)){
		foreach($gaps as $gap){
			//valor que se presentaría para elegir
			strftime("%d/%m/%Y %H:%M:%S", $gap[0]).'-'.($gap[1] ? strftime("%d/%m/%Y %H:%M:%S", $gap[1]) : null);
		}
	} else {
		$unlastedFrame = 1;
	}
}

/**
 * Function which changes the state of a node until a final stateID
 *
 * @param int $nodeID node to be moved from a state to another
 * @param int $stateID final STATE
 * @param array $listausers user id's array
 * @param int $uID user who perform the change
 * @param string $texttosend comment included in the mail to send
 */
function baseIO_CambiarEstado($nodeID, $stateID, $listausers=null, $uID=null, $texttosend="") {
	$node=new Node($nodeID);
	$estadoactual=$node->GetState();
	$workflowFin = new Workflow($nodeID, $estadoactual);
	$workflowFin->SetID($stateID);
	$estadoprevio=$workflowFin->GetPreviousState();
	$workflowIni = new Workflow($nodeID, $estadoprevio);

	//it will go from $workflowIni->GetName() to $workflowFin->GetName()
//	echo "<br>MOVE FORWARD FROM.$workflowIni->GetName()." TO ".$workflowFin->GetName();
	$jap_estadoprevio = $workflowIni->GetName();

	//Changing the state of the indicated node
	$node->SetState($stateID);
	if (!$node->numErr) {
		XMD_Log::info(_('State successfully changed'));
	} else {
		XMD_Log::info(_('Error changing state: ').$node->msgErr);
	}

	//Notifiying the selected users
	if ($listausers){
		$user = new User();
		foreach($listausers as $userID){
			$user->SetID($userID);
			if ($to) {
				$to = $to . "," . $user->GetLogin();
			} else {
				$to = $user->GetLogin();
			}
		}
		//echo "debug: $to";

		XMD_Log::info(_('Sending notification to the following users:  $to'));

		$user->SetID($uID);
		$from=$user->GetLogin();
		$jap_doc = $node->GetNodeName();

		$subject =_('Ximdex document new state:')." ".$jap_doc;

		$content =_('State forward notification.')."\n\n";

		$content.=_('The user')." `". $user->GetRealName(). "´ "._('has changed the state of the document')." `".$jap_doc."´\n\n";
		$content.=_('Full path')."  --> ". $node->GetPath() ."\n\n";

		$content.=_('Initial state')." --> ". $jap_estadoprevio ."\n";
		$content.=_('Final state')." --> ". $workflow->GetName()."\n";

		$content.= "\n\n"._('Comment').":\n". $texttosend. "\n";

		$msg = new MesgEvent($from, $from, $to, $subject, $content);
		//echo "debug: imail: ($from), ($to), ($subject), ($content)";
		$msg->Send();
		if (!$msg->numErr) {
			XMD_Log::info(_('Message sent successfully'));
		} else {
			XMD_Log::info(_('Error sending message').": ".$msg->msgErr);
		}

		foreach($listausers as $userID){
			$user->SetID($userID);
			$header= "From: Ximdex <ximdex>\r\n";
			$email=mail($user->GetEmail(),$subject, $content,$header);
			//echo "debug: email to ".$user->GetEmail().", ($subject), ($content)";
			if($email) {
				XMD_Log::info(_('Message successfully sent to ').$user->GetEmail());
			} else {
				XMD_Log::info(_('Error sending e-mail').": ".$user->GetEmail());
			}
		}
	}
}
function baseIO_PublicarDocumento($nodeID, $up, $down, $markEnd=null){
	$node=new Node($nodeID);
	$sync = new Synchronizer($nodeID);
	$sync->CreateFrame($up, $down, $markEnd);
	$data = new DataFactory($nodeID);
	$data->AddVersion(true);

	//The document $node->GetNodeName() is going to be published and go back to initial state
	if ($sync->numErr) {
		XMD_Log::info(_('Error sending to publish: ').$sync->msgErr);
	} else {
		XMD_Log::info(_('Sent to publish successfully'));
		$node->SetState($node->nodeType->GetInitialState());
		if (!$node->numErr) {
			XMD_Log::info(_('State successfully changed'));
		} else {
			XMD_Log::info(_('Error changing state'));
		}
	}
}
?>