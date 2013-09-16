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



/**
 *  Filter config
 *  some ways to display
 **/

// Dimensions must be consistent with 'unit' key

$formpdf_config = array(

	"DIN-A4" => array(
					'name' => 'DIN A4',
					'width' => 210,
					'height' => 297,
					'unit' => 'px'
				),
	
	"DIN-A5" => array(
					'name' => 'DIN A5',
					'width' => 148,
					'height' => 210,
					'unit' => 'px'
				),
	
	"US-LETTER" => array(
					'name' => 'US Letter',
					'width' => 215.9,
					'height' => 279.4,
					'unit' => 'px'
				)
);

?>
