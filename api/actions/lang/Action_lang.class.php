<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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
ModulesManager::file('/inc/model/language.inc');

 /* <p>API language action</p>
 * <p>Handles requests to obtain the languages</p>
 */
class Action_lang extends AbstractAPIAction implements SecuredAction {

    /**
     * <p>Default method for this action</p>
     * <p>Gets all registered languages or a specific one</p>
     * @param Request The current request
     * @param Response The Response object to be sent and where to put the response of this action
     */
    public function index($request, $response) {
        $langId = $request->getParam("langid");

        if ($langId == null || $langId == "") {
            $langs = $this->getLanguageInfo();
        } else {
	    $l = new Language($langId);
            if ($l->GetID() == null) {
                $this->createErrorResponse("The language ID given is not a existing language.");
                return;
            }
            
            $langs = $this->getLanguageInfo($l->GetID());
        }

        if (empty($langs)) {
            $this->createErrorResponse("No languages found");
            return;
        }

        $this->responseBuilder->ok()->content($langs)->build();
    }

    /**
     * <p>Gets the valid languages for the given node</p>
     * @param Request The current request
     * @param Response The Response object to be sent and where to put the response of this action
     */
    public function node($request, $response) {
        $nodeid = $request->getParam('nodeid');
        $username = $request->getParam(self::USER_PARAM);
        $node = new Node($nodeid);

        if ($nodeid == null) {
            $this->createErrorResponse('The nodeid parameter is missing');
            return false;
        }
        if ($node->GetID() == null) {
            $this->createErrorResponse('The node ' . $nodeid . ' does not exist');
            return false;
        }

        $nodeService = new NodeService();
        
        $hasPermissionOnNode = $nodeService->hasPermissionOnNode($username, $nodeid);
        
        if (!$hasPermissionOnNode) {
            $this->createErrorResponse('The user does not have permission on node ' . $nodeid);
            return false;
        }
        
        $lang = new Language();
        $langs = $lang->getLanguagesForNode($nodeid);
        
        if(empty($langs) || $langs == null) {
            $this->createErrorResponse('No languages found for the node');
            return;
        }
        
        $this->responseBuilder->ok()->content($langs)->build();
    }

    /**
     * <p>Checks whether the required parameters are present in the request
     * and modifies the response accordingly</p>
     * 
     * @param $request the request
     * @param $response the response
     * @return true if all required parameters are present and valid and false otherwise
     */
    private function checkParameters($request, $response) {


        $node = new Node($nodeid);

        if ($nodeid == null) {
            $this->createErrorResponse('The nodeid parameter is missing');
            return false;
        }
        if ($node->GetID() == null) {
            $this->createErrorResponse('The node ' . $nodeid . ' does not exist');
            return false;
        }

        $hasPermissionOnNode = $user->HasPermissionOnNode($nodeid, "View all nodes");
        if (!$hasPermissionOnNode) {
            $this->createErrorResponse('The user does not have permission on node ' . $nodeid);
            return false;
        }

        return true;
    }

    /**
     * <p>Gets the registered languages or a specific language if a language id is given</p>
     * @param int $lang The lang id
     * @return array containing the requested languages
     */
    private function getLanguageInfo($langId = null) {

        $lang = new Language();
        $langs = array();
        if ($langId != null && $langId != "") {
            $lang->SetID($langId);
            $langItem = array(
                'IdLanguage' => $langId,
                'Name' => $lang->get('Name'),
                'IsoCode' => $lang->get('IsoName')
                    );
            array_push($langs, $langItem);
        }

        else {
            $langsIds = $lang->GetAllLanguages();
            foreach($langsIds as $langItemId) {
                $l = new Language($langItemId);
                $langItem = array(
	                'IdLanguage' => $l->get('IdLanguage'),
        	        'Name' => $l->get('Name'),
                	'IsoName' => $l->get('IsoName')
                    );
            array_push($langs, $langItem);
                
            }
        }
        return $langs;
    }

}

?>