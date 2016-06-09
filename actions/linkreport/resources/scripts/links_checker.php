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


use Ximdex\Models\Link;
use Ximdex\Runtime\Db;
include_once dirname(__FILE__) . '/../../../../bootstrap/start.php';

function main ($argc, $argv){
    // Command line mode call
	if ($argv != null && isset($argv[1]) && is_numeric($argv[1])) {
            $idlink = $argv[1];
            $link = new Link($idlink);
            if ($link->get("IdLink")){
                $checkResult = checkLink($link->get("Url"), $idlink)? Link::LINK_OK: Link::LINK_FAIL;;
                updateLinkState($idlink, $checkResult);
            }
        }else{
            $dbObj = new Db();
            $sql = "SELECT IdLink,Url FROM Links";
            $dbObj->Query($sql);

            while (!$dbObj->EOF) {
                $idlink = $dbObj->GetValue('IdLink');
                $url = $dbObj->GetValue('Url');
                $checkResult = checkLink($url, $idlink)? Link::LINK_OK: Link::LINK_FAIL;
                updateLinkState($idlink, $checkResult);
                $dbObj->Next();
            }
        }
        die();
}

function checkLink($url,$idLink){
    $result = false;
    $link = new Link($idLink);
    if ($link->get("IdLink")){
        $url = $link->get("Url");
        echo "\n\n[".date("H:i:s d/m/y")."] (Id: ".$idLink.") -> ".$url;
    }
    
    $headers=get_headers($url,1);
    if($headers[0]!=""){
            echo "\nStatus:".$headers[0];
            $pos1 = strpos($headers[0], '200');
            $pos2 = strpos($headers[0], '301');
            $pos3 = strpos($headers[0], '302');
            if(($pos1!==false)||($pos2!==false)||($pos3!==false)){
                $result = true;
            }
    }
    return $result;
}

function updateLinkState($idLink, $errorString){
    $dbObj = new Db();
    $sql = "UPDATE Links SET ErrorString='$errorString',CheckTime=".time()." WHERE IdLink=$idLink";
    $dbObj->Execute($sql);
}

main($argc, $argv);
?>