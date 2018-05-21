#!/usr/bin/env php
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

require_once dirname(__DIR__) . '/bootstrap.php';

use Ximdex\Logger;
use Ximdex\Models\Node;
use Ximdex\Sync\Synchronizer;


/// Array with call parameters 
$config = array();

/// Call to main procedure
Main($argv, $argc);
function DoPublicate($sectionID, $recursive = false) {
	$node = new Node($sectionID);
	$childList = $node->GetChildren();

	if ($childList) {
		Logger::display(_("Precalculating list of nodes to expire"));
		foreach($childList as $child) {
			$childNode = new Node($child);
			//It adds children nodes except it they are of section type and they have not been specified like recursives
			if($recursive || ( $childNode->nodeType->GetName() != "Section") ) {
				$childList = array_merge($childList, $childNode->TraverseTree());
			}
		}
		
		foreach($childList as $nodeID) {
			Logger::display("");
			Logger::display("---------------------------------------------------------------------");
			Logger::display(_("Expiring node '").$nodeID."'");
			$sync = new Synchronizer($nodeID);
			$sync->deleteFramesFromNow($nodeID);

		}
	}
}

function Main($argv, $argc)
	{
	global $config;
	
	Logger::display("---------------------------------------------------------------------");
	Logger::display(_("Executing: Expire section"));
	Logger::display("---------------------------------------------------------------------");
	
	
	$node		= new Node();
	/// Saving and checking parameters
	for($i=1; $i<sizeof($argv); $i++)
		{
		if($argv[$i] == "--sectionid")
			{
			$node->SetID($argv[$i+1]);

			if(!$node->numErr)
				$config['sectionid'] = $argv[++$i];
			else
				{
				Logger::display(_("Section does not exist: '").$argv[++$i]."'");
				exit(1);
				}
			}
			
		}

	$rec=$argv[3];

	Logger::display("");
	
	if(!$config['sectionid'])
		{
		Logger::display(_("Uso del comando:"));
		Logger::display(_("./expiresection_IO.php --sectionid {id de la seccion} [-r]"));
		exit(1);
		}
	Logger::display("---------------------------------------------------------------------");
	Logger::display(_("Read parameters: "));
	Logger::display(_("\t\tXimdex section: ").$config['sectionid'].", ".$node->GetNodeName());
	Logger::display("---------------------------------------------------------------------");
	//var_dump($config);

	Logger::display("");
	Logger::display(_(" Are read parameters correct?"));
	Logger::display(_("To confirm press uppercase 'A' and then press 'Intro'."));
	Logger::display(_(" Press Ctrl+C to exit."));
	$stdin = fopen('php://stdin', 'r');
	do	{
		;
		} while(fread($stdin, 1)!= 'A');
	fclose($stdin);	

	$recursive = $rec=="-r";
	DoPublicate($config['sectionid'], $recursive);
	
}