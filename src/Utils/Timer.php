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

namespace Ximdex\Utils;


// TODO: Detect web or CLI context to define output type.

/**
 * Class Timer
 * @package Ximdex\Utils
 */
class Timer
{

    /**
     * @var int
     */
    var $_start = 0;

    /**
     * @var int
     */
    var $_end = 0;

    /**
     * @var
     */
    var $_parcials;

    /**
     * @var
     */
    var $_p_idx;

    /**
     * @var
     */
    var $word;


    /**
     *
     */
    public function __constructor()
    {
        $this->_parcials = array();
        $this->_p_idx = 0;
    }

    /**
     *
     */
    function start()
    {
        $this->_start = $this->microtime_float();
    }

    /**
     * @return float
     */
    function microtime_float()
    {
        list($useg, $seg) = explode(" ", microtime());
        return ((float)$useg + (float)$seg);
    }

    /**
     *
     */
    function stop()
    {
        $this->_end = $this->microtime_float();
    }

    /**
     * @param string $msg
     * @return mixed
     */
    function mark($msg = '')
    {
        $idx = $this->_p_idx++;
        $this->_parcials[$idx]['time'] = $this->microtime_float();
        $this->_parcials[$idx]['msg'] = $msg;
        $this->_parcials[$idx]['total'] = $this->convertUnits($this->_parcials[$idx]['time'] - $this->_start);


        return $this->_parcials[$idx]['total'];
    }

    /**
     * @param $total
     * @param string $unit
     * @return float|string
     */
    function convertUnits($total, $unit = 'ms')
    {

        switch ($unit) {
            case 's':
                $this->word = ' seconds';
                break;
            case 'm':
                $total = ($total / 60);
                $this->word = ' minutes';
                break;
            default:
                $total = ($total * 1000);
                $this->word = ' milliseconds';
                break;
        }


        $total = number_format($total, 6, '.', '');

        return $total;
    }

    /**
     * @param string $unit
     * @return float|int|string
     */
    function display($unit = 'ms')
    {
        $total = $this->_end - $this->_start;
        $total = $this->convertUnits($total, $unit);

        return $total;
    }

    /**
     * @param string $unit
     * @param bool $onlyPartial
     * @return float|string
     */
    function display_parcials($unit = 'ms', $onlyPartial = false)
    {
        $delay = null ;

        $sms = "Tiempos parciales: \n ";

        foreach ($this->_parcials as $idx => $data) {

            $idx_prev = $idx - 1;
            if (!key_exists($idx_prev, $this->_parcials)) {
                $time_start = $this->_start;
            } else {
                $time_start = $this->_parcials[$idx_prev]['time'];
            }

            $time_end = $data['time'];
            $delay = $time_end - $time_start;
            $delay = $this->convertUnits($delay, $unit);
            $msg = $data['msg'];

            $sms .= $msg . " --> " . $delay . " $unit\n";
        }

        return ($onlyPartial === false) ? $sms : $delay;
    }
}
