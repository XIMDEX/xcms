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
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../../../'));
}
require_once(XIMDEX_ROOT_PATH.'/inc/modules/ModulesManager.class.php');
ModulesManager::file('/actions/remove/inc/RemoveCli.class.php', 'ximIO');
ModulesManager::file('/inc/cli/CliReader.class.php');
ModulesManager::file('/inc/db/db.inc');
ModulesManager::file('/inc/fsutils/FsUtils.class.php');


$parameterCollector = new RemoveCli($argc, $argv);


$file = $parameterCollector->getParameter('--file');
$delete = $parameterCollector->getParameter('--delete');
$messages = new Messages();
// First, checking if the package exists and giving some information about it

echo "\n"._("Analysing data:")."\n";

if ($delete != 'ONLY_DB') {
	echo _("Checking if the importation folder exists")."\n";
	
	$folder = sprintf(XIMDEX_ROOT_PATH . '/data/backup/%s_ximio', $file);
	if (is_dir($folder)) {
		$messages->add(sprintf(_("The data of the package %s are going to be deleted"), $file), MSG_TYPE_NOTICE);
	} else {
		unset($folder);
		$messages->add(sprintf(_("The package %s has been deleted from the folder")." \$XIMDEX/data/backup", $file), MSG_TYPE_WARNING);
	}
}

if ($delete != 'ONLY_FILES') {
	echo _("Checking package in database...")."\n";
	
	$dbObj = new DB();
	$query = sprintf("SELECT idXimIOExportation FROM XimIOExportations where timeStamp = %s", $dbObj->sqlEscapeString($file));
	$dbObj->Query($query);
	if (!($dbObj->numRows > 0)) {
		$messages->add(_('Importation information not found in database'), MSG_TYPE_WARNING);
	} else if ($dbObj->numRows === 1) {
		$idXimioExportation = $dbObj->GetValue('idXimIOExportation');
		$messages->add(sprintf(_("Importation package %s is going to be deleted from ximIO association tables"), $idXimioExportation), MSG_TYPE_NOTICE);
	} else {
		$messages->add(_("Unexpected error occured, several packages with same name were found"), MSG_TYPE_ERROR);
	}
}
	
if ($messages->count(MSG_TYPE_ERROR) > 0) {
	echo _("Errors:")."\n";
	$messages->displayRaw(MSG_TYPE_ERROR);
}

if ($messages->count(MSG_TYPE_WARNING) > 0) {
	echo _("Warnings:")."\n";
	$messages->displayRaw(MSG_TYPE_WARNING);
}

if ($messages->count(MSG_TYPE_NOTICE) > 0) {
	echo _("Information:")."\n";
	$messages->displayRaw(MSG_TYPE_NOTICE);
}


if ($messages->count(MSG_TYPE_WARNING) > 0 || $messages->count(MSG_TYPE_ERROR) > 0) {
	$continueOptions = array('y', 'Y', 's', 'S');
	$abortOptions = array('n', 'N');
	$continueMessage = _("Errors and Warnings occured, do you want to continue with the process? (Y/n)")."\n";
	$abortMessage = _("Aborting process due to user request.")."\n";
	if (!CliReader::alert($continueOptions, $continueMessage, $abortOptions, $abortMessage)) {
		die();
	}
}


// TODO re-make using fsutils::deltree
$messages = new Messages();
function removeFolder($dir, $DeleteMe) {
	global $messages;
    if(!$dh = opendir($dir)) return;
    while (($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        $file = $dir.'/'.$obj;
        if (is_dir($file)) {
        	removeFolder($file, true);
        } else {
	        if (!FsUtils::delete($file)) {
	        	$messages->add(sprintf(_("Error deleting the file %s"), $file), MSG_TYPE_WARNING);
	        }
        }
    }
    if ($DeleteMe){
        closedir($dh);
        if (!rmdir($dir)) {
        	$messages->add(sprintf(_('Error al eliminar la carpeta %s'), $dir), MSG_TYPE_WARNING);
        }
    }
}

echo _("Deleting information about the package ")."$file\n";

if (isset($folder)) {
	removeFolder($folder, true);
	echo "Carpeta eliminada\n";
} else {
	$messages->add(_('Folder information will not be deleted'), MSG_TYPE_NOTICE);
}

if (isset($idXimioExportation)) {
	$dbObj = new DB();
	$query = "DELETE FROM XimIOExportations WHERE idXimIOExportation = " . $dbObj->sqlEscapeString($idXimioExportation);
	$dbObj->Execute($query);
	
	if ($dbObj->numRows > 0) {
		$messages->add(_('Data successfully deleted from XimIOExportations'), MSG_TYPE_NOTICE);
	} else {
		$messages->add(_('Importation data could not been deleted from XimIOExportations'), MSG_TYPE_NOTICE);
	}
	
	$dbObj = new DB();
	$query = "DELETE FROM XimIONodeTranslations WHERE IdXimioExportation = " . $dbObj->sqlEscapeString($idXimioExportation);
	$dbObj->Execute($query);
	
	if ($dbObj->numRows > 0) {
		$messages->add(sprintf(_('%s associations successfully deleted from XimIONodeTranslations'), $dbObj->numRows), MSG_TYPE_NOTICE);
	} else {
		$messages->add(_('Associations could not been deleted from XimIONodeTranslations'), MSG_TYPE_NOTICE);
	}
} else {
	$messages->add(_('Importation information will not be deleted'), MSG_TYPE_NOTICE);
}

$messages->displayRaw();

?>
