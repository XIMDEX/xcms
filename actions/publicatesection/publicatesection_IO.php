#!/usr/bin/php -q
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




ModulesManager::file('/inc/utils.php');

if (ModulesManager::isEnabled('ximSYNC')) {							
	ModulesManager::file('/inc/manager/SyncManager.class.php', 'ximSYNC');
} else {
	ModulesManager::file('/inc/sync/SyncManager.class.php');
}
$userximio = "ximIO";
session_id("ximIO");
session_name("ximIO");
XSession::set("userID",$userximio);

/// Array with call parameters
$config = array();

/// Calls main process
Main($argv, $argc);


function Main($argv, $argc)
	{
	global $config;
	
	XMD_Log::info(_("Launching publicate section"));

	XMD_Log::display("---------------------------------------------------------------------");
	XMD_Log::display(_("Executing: Publicate Section"));
	XMD_Log::display("---------------------------------------------------------------------");
	
	
	$node		= new Node();
	$ximConfig	= new Config();
	/// Saves and checks parameters


	for($i=1; $i<sizeof($argv); $i++)
		{

		if($argv[$i] == "--sectionid")
			{

			$node->SetID($argv[$i+1]);

			if(!$node->numErr)
				$config['sectionid'] = $argv[++$i];
			else
				{
				XMD_Log::display(_("Section does not exist: '").$argv[++$i]."'");
				exit(1);
				}
			}
			
		}
	$rec=$argv[3];


	XMD_Log::display("");
	
	if(!$config['sectionid'])
		{
		XMD_Log::display(_("Uso del comando:"));
		XMD_Log::display("./publicatesection_IO.php --sectionid {id de la seccion} [-r]");
		exit(1);
		}
	XMD_Log::display("---------------------------------------------------------------------");
	XMD_Log::display(_("Read parameters: "));
	XMD_Log::display(_("\tXimdex section: ").$config['sectionid'].", ".$node->GetNodeName());
	XMD_Log::display("---------------------------------------------------------------------");
	//var_dump($config);

	XMD_Log::display("");
	XMD_Log::display(_(" Are read parameters corrects?"));
	XMD_Log::display(_("To confirm press uppercase 'A' and then press 'Intro'."));
	XMD_Log::display(_(" Press Ctrl+C to exit."));

	session_write_close();
	ob_flush();

	$stdin = fopen('php://stdin', 'r');
	do	{
		;
		} while(fread($stdin, 1)!= 'A');
	fclose($stdin);	

	XSession::set("userID",$userximio);

	if ($rec=="-r") {
		PublicateSection($config['sectionid'], time(), true);
	}
	else {
		PublicateSection($config['sectionid'], time(), false);
	}
	
	XMD_Log::info("Saliendo de publicate section");
}


function PublicateSection($sectionID,$dateUp,$recurrence) {

        $node = new Node($sectionID);
	if (!($node->nodeType->GetName() == 'Section' || $node->nodeType->GetName() == 'Server')) {
	    XMD_Log::display(_("Aborting publication, it is just allowed to publish over sections and servers"));
	    die();
	}

        $nodename=$node->GetNodeName();
        $childList = $node->GetChildren();

        $nodeList= array();
	$arrayTypes = array('Section','XimNewsSection');

        foreach ($childList as $child) {
                $node->SetID($child);
		if($recurrence == 1 || (!in_array($node->nodeType->GetName(),$arrayTypes) && $recurrence == 0)){
                        $nodeList = array_merge($nodeList, $node->TraverseTree(6));
                }
        }

        foreach ($nodeList as $nodeID) {
		XMD_Log::info("Publicando IdNode: $nodeID");
		$syncMngr = new SyncManager();

		$syncMngr->setFlag('markEnd', false);

		// bug: previously it passes as false in the workflow, avoiding publication of
		//      documents associate with document by workflow.
		//$syncMngr->setFlag('workflow', true);

		$syncMngr->setFlag('linked',true);
		$syncMngr->setFlag('deleteOld',true);

		$syncMngr->pushDocInPublishingPool($nodeID, $dateUp, NULL);
        }
}

?>