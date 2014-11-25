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


/**
 *
 */
class ModulesConfig
{

    // Object composition.
    var $defMngr;

    /**
     * @public
     */
    public function __construct()
    {

        $this->defMngr = new \Ximdex\Modules\DefManager(\App::getValue('XIMDEX_ROOT_PATH') . ModulesManager::get_modules_install_params());
        $this->defMngr->setPrefix(ModulesManager::get_pre_define_module());
        $this->defMngr->setPostfix(ModulesManager::get_post_define_module());
    }


    /**
     * @public
     */
    function enableModule($name)
    {
        $this->defMngr->enableItem(strtoupper($name));
    }

    /**
     * @public
     */
    function disableModule($name)
    {

        $this->defMngr->disableItem(strtoupper($name));
    }

}
