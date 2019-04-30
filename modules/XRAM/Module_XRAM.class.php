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
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

use Ximdex\Modules\Module;

class Module_XRAM extends Module
{
    public function __construct()
    {
        // Call Module constructor
        parent::__construct('XRAM', dirname(__FILE__));
        
        // Initialization stuff
    }

    public function install()
    {
        // Install logic
        // Get module from ftp, webdav, subversion, etc...?
        // Need to be extracted?
        // Extract and copy files to modules location.
        // Get constructor SQL   
        $this->loadConstructorSQL("XRAM.constructor.sql");
        return parent::install();
    }

    public function uninstall()
    {
        // Uninstall logic
        // Get destructor SQL          
        $this->loadDestructorSQL("XRAM.destructor.sql");

        // Uninstall !      
        parent::uninstall();
    }

    /**
     * Check curl extension and Solr PECL extension
     * 
     * {@inheritDoc}
     * @see \Ximdex\Modules\Module::preInstall()
     */
    public function preInstall()
    {
        // PHP-CURL
        if (!extension_loaded('curl')) {
            return false;
        }
        return true;
    }
}
