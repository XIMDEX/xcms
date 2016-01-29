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

use Ximdex\MVC\ActionAbstract;


class Action_reportslist extends ActionAbstract
{
    public function index()
    {
        $reports = $this->getReports();
        $values = array('reports' => $reports);

        $this->render($values, null, 'only_template.tpl');

        //header('Content-type: application/json');
        //echo Serializer::encode(SZR_JSON, $data);
    }

    protected function getReports()
    {
        $reports = array(
            array(
                'name' => 'managebatchs',
                'mod' => 'ximPUBLISHtools',
                'action' => 'managebatchs',
                'method' => 'batchlist'
            ),
            array(
                'name' => 'viewcolectorstates',
                'mod' => 'ximPUBLISHtools',
                'action' => 'viewcolectorstates',
                'method' => 'index'
            )
        );

        return $reports;
    }
}
