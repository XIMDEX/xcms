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



	

ModulesManager::file('/inc/ExportXml.class.php', 'ximIO');
ModulesManager::file('/inc/ImportXml.class.php', 'ximIO');
ModulesManager::file('/inc/FileUpdater.class.php', 'ximIO');

	
	function copyNode ($source, $dest, $recurrence) {
		
		$messages = new Messages();
		
		// Checking if source is allowed on the destiny to save addicional operations
		
		$sourceNode = new Node($source);
		if (!($sourceNode->get('IdNode') > 0)) {
			$messages->add(_('Source node does not exist'), MSG_TYPE_ERROR);
			return $messages;
		}else{
		  $lastName = $sourceNode->get('Name');
		}
		
		$destNode = new Node($dest);
		if (!($destNode->get('IdNode') > 0)) {
			$messages->add(_('Destination node does not exist'), MSG_TYPE_ERROR);
			return $messages;
		}

		
		//Checking both nodes belong to same project
		//or if the node we want to copy is the root of a complete project 
		if (($sourceNode->getProject() != $sourceNode->getID()) && ($sourceNode->getProject() != $destNode->getProject())) {
			$messages->add(_('You cannot make a copy of nodes between differents projects'), MSG_TYPE_ERROR);
			return $messages;
		}
		
		$nodeAssociations = NULL;
		
		// 1.- Getting data to export

		$xmlExporter = new ExportXml($source);
		if ($xmlExporter->messages->count(MSG_TYPE_ERROR) > 0) {
			return $xmlExporter->messages;
		}
		
		

		$files = null;
		$xml = $xmlExporter->getXml($recurrence, $files);
		unset($xmlExporter);

		// Checking if processFirstNode should be called as true or false
		
		if ($destNode->nodeType->get('IdNodeType') == $sourceNode->nodeType->get('IdNodeType')) {
			$processFirstNode = true;
		} else {
			$dbObj = new DB();
			$query = sprintf('SELECT Amount from NodeAllowedContents'
				. ' WHERE IdNodeType = %s AND NodeType = %s',
				$dbObj->sqlEscapeString($destNode->nodeType->get('IdNodeType')), 
				$dbObj->sqlEscapeString($sourceNode->nodeType->get('IdNodeType')));
			$dbObj->query($query);
			$amount = $dbObj->getValue('Amount');
			if ($amount == 1) {
				$children = $destNode->GetChildren($sourceNode->nodeType->get('IdNodeType'));
				$dest = $children[0];
				$processFirstNode = false;
			} else {
				$processFirstNode = true;
			}
		}

		// 2.- Importing the corresponding database part 
		$importer = new ImportXml($dest, NULL, $nodeAssociations, RUN_IMPORT_MODE, $recurrence, NULL, $processFirstNode);
		$importer->mode = COPY_MODE;
		$importer->copy($xml);
		reset($importer->messages);

		while(list(, $message) = each($importer->messages)) {
			$messages->add($message, MSG_TYPE_WARNING);
		}
//Add info about what was imported 

		// 3.- Importing contents (from a files array ?), it is not necessary, i have got it in database
		
		$fileImport = new FileUpdater(0);
		$fileImport->updateFiles(IMPORT_FILES);
		unset($fileImport);

		// 4.- Cleaning transform table to repeat the copy
		$dbConn = new DB();
		
		$query = sprintf("SELECT idXimIOExportation FROM XimIOExportations WHERE timeStamp = '%s'", REVISION_COPY);
		$dbConn->Query($query);
		if ($dbConn->numRows == 1) {
			$idXimIOExportation = $dbConn->GetValue('idXimIOExportation');
			
			$statusQuery = sprintf("SELECT `status`, count(*) as total"
							. " FROM XimIONodeTranslations"
							. " WHERE IdExportationNode != IdImportationNode AND idXimIOExportation = %d"
							. " GROUP BY `status`"
							. " ORDER BY `status` DESC"
							,$idXimIOExportation);
			
			$dbConn->Query($statusQuery);
			if ($dbConn->EOF) {
				$messages->add(_('An error occurred during copy process, information about process could be obtained'), MSG_TYPE_ERROR);
			} else {
				while(!$dbConn->EOF) {
					switch ($dbConn->GetValue('status')) {
						case "1":
							$messages->add(sprintf(_('%d nodes have been successfully copied'), $dbConn->GetValue('total')), MSG_TYPE_NOTICE);
							break;
						case "-1":
							$messages->add(sprintf(_('%d nodes have not been copied because of lack of permits'), $dbConn->GetValue('total')), MSG_TYPE_WARNING);
							break;
						case "-2":
							$messages->add(sprintf(_('%d nodes have not been copied because of lack of XML info'), $dbConn->GetValue('total')), MSG_TYPE_WARNING);
							break;
						case "-3":
							$messages->add(sprintf(_('%d nodes have not been copied because its parents have not been imported'), $dbConn->GetValue('total')), MSG_TYPE_WARNING);
							break;
						case "-4":
							$messages->add(sprintf(_('%d nodes have not been copied because they are not allowed into the requested parent'), $dbConn->GetValue('total')), MSG_TYPE_WARNING);
							break;
					}
					$dbConn->Next();
				}
			}

			$query = sprintf("DELETE FROM XimIONodeTranslations WHERE idXimIOExportation = %d", $idXimIOExportation);
			$dbConn->Execute($query);
			$query = sprintf("DELETE FROM XimIOExportations WHERE idXimIOExportation = %d", $idXimIOExportation);
			$dbConn->Execute($query);


		}

		$targetNode = new Node($importer->idfinal);
		$newName = $targetNode->GetNodeName();
		$nodeType = new NodeType($targetNode->nodeType->get('IdNodeType') );
		//Debug::log("id:", $importer->id, "newName:", $newName, "lastName", $lastName, "nodetype:", $nodeType->GetName() );
		if($lastName != $newName && null != $newName && ( "XmlContainer" == $nodeType->GetName() || "XimletContainer" == $nodeType->getGetName() ) ) {
		  $childrens =  $targetNode->GetChildren();
		  $total = count($childrens);
		  //Debug::log($total);
		  for($i = 0; $i< $total; $i++) {
				$children = $childrens[$i];
				$node_child = new Node($children);
				$name_child = $node_child->GetNodeName();
				$name_child = str_replace($lastName, $newName, $name_child);
				$node_child->SetNodeName($name_child);
		  }
	   }

		unset($importer);

		return $messages;

	}
?>
