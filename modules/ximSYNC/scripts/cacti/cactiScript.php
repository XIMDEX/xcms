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




exec ("ps aux", $out, $var);

$pumpStr = '';
$scheStr = '';
$str = '';

foreach ($out as $data) {
	if ( preg_match('/ ([\d]{1,2}\.\d)(\s*)([\d]{1,2}\.\d)([\w|\W]+)--pumperid (\d*) --/i',$data,$regs) ){
		$cpu = $regs[1];
		$mem = $regs[3];
		$pumperId = $regs[5];
		$pumpStr .= "pumper_cpu:$cpu pumper_mem:$mem ";
	}

	if ( preg_match('/ ([\d]{1,2}\.\d)(\s*)([\d]{1,2}\.\d)([\w|\W]+)scheduler/i',$data,$regs) ) {
		$cpu = $regs[1];
		$mem = $regs[3];
		$scheStr = "scheduler_cpu:$cpu scheduler_mem:$mem ";
	}
}

if ($scheStr == '') {
	$str .= "scheduler_cpu:0.0 scheduler_mem:0.0 ";
} else {
	$str .= $scheStr;
}

if ($pumpStr == '') {
	$str .= "pumper_cpu:0.0 pumper_mem:0.0 ";
} else {
	$str .= $pumpStr;
}

echo $str;
?>