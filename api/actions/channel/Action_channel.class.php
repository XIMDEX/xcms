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
ModulesManager::file('/inc/model/channel.inc');
//use Swagger\Annotations as SWG;

/**
 * 
 * /**
 * @SWG\Resource(
 *     apiVersion="0.2",
 *     swaggerVersion="1.1",
 *     resourcePath="/channel",
 *     basePath="http://test.ximdex.es/ximdex_new/api"
 * )
 * 
 * 
 * @SWG\Api(
 *  path="/channel",
 *  description="Operations over channels",
 *  @SWG\Operations(
 *      @SWG\Operation(
 *          httpMethod="GET",
 *          summary="Get channels",
 *          notes="Return JSON containing the requested channels",
 *          nickname="channel/index",
 *          @SWG\Parameters(
 *              @SWG\Parameter(name="ximtoken", description="The api token", paramType="query", required="true", allowMultiple="false", dataType="string"),
 *              @SWG\Parameter(name="channelid", description="The id of the channel", paramType="query", required="false", allowMultiple="false", dataType="int")
 *          )
 *      )
 *  )
 * )
 * 
 * @SWG\Api(
 *  path="/channel/node",
 *  description="Operations over channels",
 *  @SWG\Operations(
 *      @SWG\Operation(
 *          httpMethod="GET",
 *          summary="Get channels on node",
 *          notes="Return JSON containing the requested channels on node",
 *          nickname="channel/node",
 *          @SWG\Parameters(
 *              @SWG\Parameter(name="ximtoken", description="The api token", paramType="query", required="true", allowMultiple="false", dataType="string"),
 *              @SWG\Parameter(name="nodeid", description="The id of the node", paramType="query", required="false", allowMultiple="false", dataType="int")
 *          )
 *      )
 *  )
 * )
 * 
 * <p>API Channel action</p>
 * <p>Handles requests to obtain the channels</p>
 */
class Action_channel extends AbstractAPIAction implements SecuredAction {

    /**
     * <p>Default method for this action</p>
     * <p>Gets all registered channels or a specific channel</p>
     * @param Request The current request
     * @param Response The Response object to be sent and where to put the response of this action
     */
    public function index($request, $response) {
        $channelId = $request->getParam("channelid");
        if ($channelId == null || $channelId == "") {
            $channels = $this->getChannelInfo();
        } else {
	    $c = new Channel($channelId);
            if ($c->GetID() == null) {
                $this->createErrorResponse("The channel ID given is not a channel.");
                return;
            }
            
            $channels = $this->getChannelInfo($c->GetID());
        }

        if (empty($channels)) {
            $this->createErrorResponse("No channels found");
            return;
        }

        $this->responseBuilder->ok()->content($channels)->build();
    }

    /**
     * <p>Gets the valid channels for the given node</p>
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
        
        $channel = new Channel();
        $channels = $channel->getChannelsForNode($nodeid);
        
        if(empty($channels) || $channels == null) {
            $this->createErrorResponse('No channels found for the node');
            return;
        }
        
        $this->responseBuilder->ok()->content($channels)->build();
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
     * <p>Gets the registered channels or a specific channel if a channel id is given</p>
     * @param int $channel The chanel id
     * @return array containing the requested channels
     */
    private function getChannelInfo($channelId = null) {

        $channel = new Channel();
        $channels = array();

        if ($channelId != null && $channelId != "") {
            $channel->SetID($channelId);
            $channelItem = array(
                'IdChannel' => $channelId,
                'Name' => $channel->get('Name'),
                'Description' => $channel->get('Description')
                    );
            array_push($channels, $channelItem);
        }

        else {
            $channelsIds = $channel->GetAllChannels();
            foreach($channelsIds as $channelItemId) {
                $ch = new Channel($channelItemId);
                $channelItem = array(
                'IdChannel' => $ch->get('IdChannel'),
                'Name' => $ch->get('Name'),
                'Description' => $ch->get('Description')
                    );
            array_push($channels, $channelItem);
                
            }
        }
        return $channels;
    }

}

?>