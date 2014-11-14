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




 

function baseIO_listarVersiones($nodeID) {

	//Returns a list with all versions of a node

	$node = new Node($nodeID);
	$data = new DataFactory($nodeID);
	$versions = $data->GetVersionList();
	
	$str="";
	
	foreach($versions as $version)
		{
		$str=_("Repositorio de Versiones del nodo ").$nodeID."\n";
		
		$str.=_("- Version ").$version." -\n";
		$str.=_("Version     Fecha                      Usuario")."\n";
		
		$subVersions = $data->GetSubVersionList($version);
		
		foreach($subVersions as $subVersion)  {
			setlocale (LC_TIME, "es_ES"); 
			$fecha=strftime("%a, %d/%m/%G %R", $data->GetDate($version,$subVersion));
		    $user=new User($data->GetUserID($version,$subVersion));
			
			$str.=$version.".".$subVersion."        ".$fecha."   ".$user->GetRealName()."    ".$data->GetComment($version,$subVersion)."\n";
		
		}
	
	
	}
	return $str;	

}

function baseIO_RecoverVersion($nodeID, $version, $subversion) {

			//Recovers a node version

			$data = new DataFactory($nodeID);
			
			$versions = $data->GetVersionList();
			$nversions=count($versions);
			
			$subVersions = $data->GetSubVersionList($version);
			$nsubversions=count($subVersions);
			
			if (($versions[$nversions-1]==$version)&&($subVersions[$nsubversions-1]==$subversion)) {
			 //If it is the last version, and last subversion, it cannot be recovered.
				return 5; 
			}
			else {
			
				$data->RecoverVersion($version,$subversion);
				return $data->numErr;
			}
}

function  baseIO_DeleteVersion($nodeID, $version, $subversion) {

			//Deletes a node version

			$data = new DataFactory($nodeID);
			
			$versions = $data->GetVersionList();
			$nversions=count($versions);
			
			$subVersions = $data->GetSubVersionList($version);
			$nsubversions=count($subVersions);
			
			if (($nversions==1)&&($nsubversions==1)) {
				//If it just exists this version and this subversion, it does not allow to delete it
				return 5;
			}
			else {
				$data->DeleteSubversion($version,$subversion);
				return $data->numErr;
			}
}