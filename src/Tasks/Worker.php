<?php

/**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

namespace Ximdex\Tasks;

use Ximdex\Runtime\App;

class Worker 
{
    private $queueManager;
    
    private $currentExec = 0;
 
    private $methods = [];

    public function __construct()
    {
        $this->queueManager = new Manager();
        $this->queue = $this->queueManager->getQueueServer();
    }

    public function addMethod(string $name, string $function)
    {
        $this->methods[$name] = $function ;
    }

    public function run(int $maxExecs = 0)
    {
        while ($maxExecs == 0 || $this->currentExec <= $maxExecs) {
            $queueName = App::getInstance()->getRuntimeValue('queueName', 'ximdex');

            // grab the next job off the queue and reserve it
            $job = $this->queue->watch($queueName)->ignore('default')->reserve();
            $jobData = json_decode($job->getData(), false);
            $function = $jobData->function;
            $data = $jobData->user_data;
            if (array_key_exists( $function, $this->methods)) {
                $this->currentExec += $this->methods[$function]($job, $data);
                continue;
            }
            echo "{$function} -> Unknown\n";
            $this->queue->release($job);
            $this->currentExec++;
            break;
        }
    }
}
