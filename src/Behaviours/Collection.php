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

namespace Ximdex\Behaviours;

use  Ximdex\Utils\Messages;
use  Ximdex\Behaviours\AssociativeArray;

class  Collection
{

    var $behaviorCollection = null;
    /**
     * @var Messages
     */
    public  $messages = null;
    private $model;

    function __construct(& $model)
    {
        $this->model = $model;
        $this->behaviorCollection = new  AssociativeArray();
        $behaviors = $model->actsAs;
        if (is_array($behaviors)) {
            foreach ($behaviors as $behavior => $params) {
                $this->attach($behavior, $params);
            }
        }
    }

    function attach($behavior, $options)
    {

        $instancedBehavior = new $behavior($options);

        $this->behaviorCollection->$behavior = $instancedBehavior;

    }

    function call__($method, $params = null)
    {
        $behaviors = $this->behaviorCollection->getKeys();
        $result = true;
        foreach ($behaviors as $behavior) {
            // No more delegations, final method MUST be implemented in the behavior or call is discarded
            if (method_exists($this->behaviorCollection->$behavior, $method)) {
                $methodResult = $this->behaviorCollection->$behavior->$method($params[0], $params[1]);
                if (is_array($methodResult)) {
                    if (is_array($result)) {
                        $result = array_merge($result, $methodResult);
                    } else {
                        $result = $methodResult;
                    }
                } else {
                    $result = $result && $methodResult;
                }

            }
        }
        return $result;
    }

    function __get($name)
    {
        $this->messages = new  Messages();
        if ($name == 'messages') {
            $behaviors = $this->behaviorCollection->getKeys();
            foreach ($behaviors as $behavior) {
                $this->messages->mergeMessages($this->behaviorCollection->$behavior->messages);
            }
        }
        return $this->messages;
    }

    function __set($name, $params)
    {
        $this->behaviorCollection->$name = $params;
    }

    function detach($behavior)
    {
        if ($this->behaviorCollection->exist($behavior)) {
            $this->behaviorCollection->$behavior->tearDown();
            $this->behaviorCollection->del($behavior);
        }
    }

    public function __call($method, $params = array())
    {
        if (!method_exists($this, 'call__')) {
            trigger_error(sprintf( __('Magic method handler call__ not defined in %s', true), get_class($this)), E_USER_ERROR);
        } else {
            return $this->call__($method, $params);
        }
    }

}
