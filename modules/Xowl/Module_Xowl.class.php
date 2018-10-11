<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Modules\Module;
use Ximdex\Runtime\App;
use Ximdex\Cli\CliReader;
use Ximdex\Rest\Services\Xowl\Searchers\AnnotationSearcherStrategy;
use Ximdex\Rest\RESTProvider;

class Module_Xowl extends Module
{
    public function __construct()
    {
        // Call Module constructor
        parent::__construct('Xowl', dirname(__FILE__));
    }

    function install()
    {
        // Get constructor SQL
        $this->loadConstructorSQL('Xowl.constructor.sql');
        return parent::install();
    }

    function configure($key, $urlService)
    {
        App::setValue('EnricherKey', '2jpkhvda52fgffz2kv8x8cuy', true);
        App::setValue('Xowl_location', $urlService, true);
        App::setValue('Xowl_token', $key, true);
        $provider = new AnnotationSearcherStrategy;
        $ret = $provider->suggest('');
        if (empty($ret)) {
            
            // Deleting key...
            App::setValue('Xowl_location', '', true);
            App::setValue('Xowl_token', '', true);
            App::setValue('EnricherKey', '', true);
            return false;
        }
        return true;
    }

    /**
     * Ask and check LMF url
     * @return bool True if the url is ok.
     */
    private function defineLMF()
    {
        $lmfUrl = CliReader::getString('Url to LMF Server: ');
        printf("\n\nChecking LMF url...\n\n");
        $result = false;
        if ($this->checkLMFPath($lmfUrl)) {
            $sql = "REPLACE INTO Config (ConfigKey, ConfigValue) VALUES ('LMF_url', '{$lmfUrl}')";
            $db = new Ximdex\Runtime\Db();
            $db->Execute($sql);
            $result = true;
        } else {
            printf('Invalid url. Retry, please.');
        }
        return $result;
    }


    /**
     * Check LMF Path doing a request to the url
     * 
     * @param  string $lmfUrl Where LMF is installed
     * @return bool True if the url is ok
     */
    private function checkLMFPath($lmfUrl)
    {
        $result = true;
        $headers = array(
            
            // To remove HTTP 100 Continue messages
            'Expect:',
            
            // Response Format
            'Accept: application/json',
            'Content-type: text/plain');
        $restProvider = new RESTProvider();
        $pingUrl = $lmfUrl . 'users';
        try {
            $response = $restProvider->getHttp_provider()->get($pingUrl, $headers);
            if (is_array($response) && array_key_exists('http_code', $response) && $response['http_code'] == '200') {
                $result = true;
            }
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    function uninstall()
    {
        // Get destructor SQL
        $this->loadDestructorSQL('Xowl.destructor.sql');
        parent::uninstall();
    }
}
