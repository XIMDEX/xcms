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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\MVC;

use Ximdex\Runtime\App;
use Ximdex\Runtime\Session;

require_once(XIMDEX_ROOT_PATH . '/conf/stats.php');

/**
 * @brief Controller to execute and route actions
 *
 * Controller to execute and route actions
 */
class ApplicationController extends IController
{
    private $timer = null;
	
    function compose()
    {
        $stats = array();

        // Select and enroute the action
        $actionController = ActionFactory::getAction($this->request);

        // Si no existe la accion, mostramos error
        if ($actionController == NULL) {
            $actionController = $this->_error_no_action();
        } else {
            $stats = $this->actionsStatsStart();
            $actionController->execute($this->request);
        }

        // Inserts action stats
        $this->actionsStatsEnd($stats);
        $this->hasError = $actionController->hasError();
        $this->msgError = $actionController->getMsgError();
    }

    /**
     * @return ActionAbstract
     */
    function _error_no_action()
    {
        $action = $this->request->getParam("action");
        $nodeid = $this->request->getParam("nodeid");
        $actionController = new Action();
        $actionController->messages->add(_("Required action not found."), MSG_TYPE_ERROR);
        $this->request->setParam('messages', $actionController->messages->messages);
        $actionController->render($this->request->getRequests());
        return $actionController;
    }

    /**
     * @return array
     */
    function actionsStatsStart()
    {
        $actionsStats = App::getValue('ActionsStats');
        $action = $this->request->getParam("action");
        $method = $this->request->getParam("method");
        $nodeId = (int)$this->request->getParam("nodeid");
        $userId = Session::get("userID");
        
        // Starts timer for use in action stats
        $stats = array();
        if ($actionsStats == 1 && !is_null($action) && "index" == $method) {
            $this->timer = new \Ximdex\Utils\Timer();
            $this->timer->start();
            $stats = array("action" => $action, "nodeid" => $nodeId, "idStat" => 0);
        }
        return $stats;
    }

    /**
     * Inserts action stats
     * 
     * @param $stats
     */
    function actionsStatsEnd($stats)
    {
        $actionsStats = App::getValue('ActionsStats');
        $action = $this->request->getParam("action");
        $method = $this->request->getParam("method");
        if ($actionsStats == 1 && !is_null($action) && "index" == $method && $this->timer) {
            $stats_time = $this->timer->mark('End action');
            $this->send_stats($stats, $method, $stats_time);
        }
    }

    /**
     * @param $stats
     * @param $method
     * @param $duration
     */
    private function send_stats($stats, $method, $duration)
    {
        $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 1
                )
            )
        );
        if (strcmp($stats["action"], "browser3") == 0) {
            $event = "login";
        }
        else {
            $event = "action";
        }
        $remote = ACTIONS_STATS;
        $ximid = App::getValue('ximid');
        $userId = Session::get("userID");
        if (strcmp($stats["nodeid"], '') != 0) {
            $nodeid = (int) $stats["nodeid"];
        } else
            $nodeid = 0;
        $code = $stats["action"] . "_" . $method;
        $url = "$remote?eventid=$event&nodeId=$nodeid&userid=$userId&duration=$duration&ximid=$ximid&code=$code";
        @file_get_contents($url, 0, $ctx);
    }
}