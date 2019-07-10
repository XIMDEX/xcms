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


namespace Ximdex\MVC\Render;

use Xmd\Widgets\Widget;


/**
 *
 * @brief Renderer for the widget system
 *
 * Renderer for the widget system
 *
 */
class WidgetsRenderer extends AbstractRenderer
{


    public function __construct($fileName = NULL)
    {
        parent::__construct($fileName);
    }


    public function render($view = NULL)
    {
        parent::render(null);
        $params = $this->getParameters();
        return $this->process($view, $params);
    }

    public function process($source, $params)
    {

        $ret = Widget::process($source, $params);

        if ($ret === null) {
            return $source;
        }

        return $ret['tpl'];
    }
}
