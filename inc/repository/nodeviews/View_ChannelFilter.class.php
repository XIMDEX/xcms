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




require_once(XIMDEX_ROOT_PATH . '/inc/filters/Filter.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Abstract_View.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/repository/nodeviews/Interface_View.class.php');

class View_ChannelFilter extends Abstract_View implements Interface_View {
	
	private $_idChannel;
	
	public function transform($idVersion = NULL, $pointer = NULL, $args = NULL) {
		
		if (array_key_exists('CHANNEL', $args)) {
			$channel = new Channel($args['CHANNEL']);
			$this->_idChannel = $channel->get('IdChannel');
		}
	
		if (!($this->_idChannel > 0)) {
			XMD_Log::error('VIEW CHANNELFILTER: No se ha especificado el canal del nodo que quiere renderizar');
			return NULL;
		}

		$content = $this->retrieveContent($pointer);
		if ($channel->get('Filter')) {
			$filter = new Filter($channel->get('Filter'));
			$content = $filter->doFilter($content);
		}

		return $this->storeTmpContent($content);
	}
}
?>