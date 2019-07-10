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

namespace Ximdex\Models\ORM;

use Ximdex\Data\GenericData;

class ServersOrm extends GenericData
{
    public $_idField = 'IdServer';
    
    public $_table = 'Servers';
    
    public $_metaData = array(
        'IdServer' => array('type' => 'int(12)', 'not_null' => 'true', 'auto_increment' => 'true', 'primary_key' => true),
        'IdNode' => array('type' => 'int(12)', 'not_null' => 'true'),
        'IdProtocol' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'Login' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'Password' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'Host' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'Port' => array('type' => 'int(12)', 'not_null' => 'false'),
        'Url' => array('type' => 'blob', 'not_null' => 'false'),
        'InitialDirectory' => array('type' => 'blob', 'not_null' => 'false'),
        'OverrideLocalPaths' => array('type' => 'int(1)', 'not_null' => 'false'),
        'Enabled' => array('type' => 'int(1)', 'not_null' => 'false'),
        'Previsual' => array('type' => 'int(1)', 'not_null' => 'false'),
        'Description' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'idEncode' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'ActiveForPumping' => array('type' => 'int(1)', 'not_null' => 'false'),
        'DelayTimeToEnableForPumping' => array('type' => 'int(12)', 'not_null' => 'false'),
        'CyclesToRetryPumping' => array('type' => 'int(12)', 'not_null' => 'true'),
        'Token' => array('type' => 'varchar(255)', 'not_null' => 'false'),
        'Indexable' => array('type' => 'int(1)', 'not_null' => 'true'),
        'LastSitemapGenerationTime' => array('type' => 'int(12)', 'not_null' => 'false')
    );
    
    public $_uniqueConstraints = array();
    
    public $_indexes = array('IdServer');
    
    public $IdServer;
    
    public $IdNode = 0;
    
    public $IdProtocol;
    
    public $Login;
    
    public $Password;
    
    public $Host;
    
    public $Port;
    
    public $Url;
    
    public $InitialDirectory;
    
    public $OverrideLocalPaths = 0;
    
    public $Enabled = 1;
    
    public $Previsual = 0;
    
    public $Description;
    
    public $idEncode;
    
    public $ActiveForPumping = 1;
    
    public $DelayTimeToEnableForPumping;
    
    public $CyclesToRetryPumping = 0;
    
    public $Token;
    
    public $Indexable;
    
    public $LastSitemapGenerationTime;
}
