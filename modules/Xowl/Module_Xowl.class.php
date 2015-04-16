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

use Ximdex\Modules\Module;


// Point to ximdex root and include necessary class.
ModulesManager::file('/inc/model/node.php');
ModulesManager::file('/inc/cli/CliReader.class.php');
ModulesManager::file('/inc/model/orm/Channels_ORM.class.php');
ModulesManager::file('modules/Xowl/config/xowl.conf');
ModulesManager::file('/inc/rest/REST_Provider.class.php');
ModulesManager::file('/services/Xowl/searchers/AnnotationSearcherStrategy.class.php');
//Xowl is not actived in this point
//require_once(XIMDEX_ROOT_PATH . ModulesManager::path('Xowl') . '/actions/enricher/model/TagSuggester.class.php');

class Module_Xowl extends Module
{

    //Class constructor
    public function __construct()
    {
        // Call Module constructor.
        parent::__construct("Xowl", dirname(__FILE__));
    }

    function install()
    {
        // get constructor SQL
        $this->loadConstructorSQL("Xowl.constructor.sql");
        $install_ret = parent::install();
        return true;
    }

    function configure($key, $urlService){
        \App::setValue('EnricherKey', '2jpkhvda52fgffz2kv8x8cuy', true);
        \App::setValue('Xowl_location', $urlService, true);
        \App::setValue('Xowl_token', $key, true);

        $provider = new AnnotationSearcherStrategy;
        $ret = $provider->suggest('');
        /*$ra = new TagSuggester();
        $text = '';
        $ret = $ra->suggest($text, 'application/json');*/

        if (empty($ret)) {
            //Deleting key...
            \App::setValue('Xowl_location', '', true);
            \App::setValue('Xowl_token', '', true);
            \App::setValue('EnricherKey', '', true);
            return false;
        }
        return true;
    }

    /**
     * Enable function. Ask Xowl Key and LMF path if requires.
     */
/*    function enable()
    {

        //Asking Xowl key.
        $sp = "You must type the Xowl key in order to activate this module.\n\n(If you don't know what it's all about, please contact us at soporte@ximdex.com.)";

        $key = CliReader::getString(sprintf("\nXowl module activation info: %s\n\n--> Xowl Key: ", $sp));
        printf("\nStoring your personal key ...\n");

        $sql = "UPDATE Config SET ConfigValue='" . $key . "' WHERE ConfigKey='EnricherKey'";
        $db = new DB();
        $db->Execute($sql);
        printf("Key stored successfully!. Testing service conection ...\n\n");

        $ra = new Enricher();
        $text = '';
        $ret = $ra->suggest($text, $key, 'xml');
        $installationOk = true;
        if (empty($ret)) {
            $installationOk = false;
            printf("Deleting key...\n");
            $sql_del = "UPDATE Config SET ConfigValue='' WHERE ConfigKey='EnricherKey'";
            $db->Execute($sql_del);
            printf("The service could not be connected. Your key is not correct. Please contact us.\n\n");
        } else {
            $installationOk = true;
            printf("Conection OK.");
            //Installing LMF if user requires.
            do {
                $isAnswerRight = true;
                $isLmfUrl = CliReader::getString("\n\nDo you want to define a LMF Service? [Y/n]: ");
                switch (strtolower($isLmfUrl)) {
                    case 'y':
                        $installationOk = $this->defineLMF();
                        if (!$installationOk)
                            $isAnswerRight = false;
                        break;
                    case 'n':
                        break;
                    default:
                        $isAnswerRight = false;
                }

            } while (!$isAnswerRight);


            if ($installationOk) {
                printf("You can now enrich your documents with our Remote Annotator!.\n\n");
            }
        }
    }
*/
    /**
     * Ask and check LMF url
     * @return bool True if the url is ok.
     */
    private function defineLMF()
    {
        $lmfUrl = CliReader::getString("Url to LMF Server: ");
        printf("\n\nChecking LMF url...\n\n");
        $result = false;
        if ($this->checkLMFPath($lmfUrl)) {
            $installationOk = true;
            $sql = "REPLACE INTO Config (ConfigKey, ConfigValue) VALUES ('LMF_url','{$lmfUrl}')";
            $db = new DB();
            $db->Execute($sql);
            $result = true;
        } else {
            printf("Invalid url. Retry, please.");
        }

        return $result;
    }


    /**
     * Check LMF Path doing a request to the url.
     * @param  string $lmfUrl Where LMF is installed
     * @return bool         True if the url is ok.
     */
    private function checkLMFPath($lmfUrl)
    {

        $result = true;
        $headers = array(
            //To remove HTTP 100 Continue messages
            'Expect:',
            //Response Format
            'Accept: application/json',
            'Content-type: text/plain');

        $restProvider = new REST_Provider();
        $pingUrl = $lmfUrl . "users";
        try {
            $response = $restProvider->getHttp_provider()->get($pingUrl, $headers);
            if (is_array($response) && array_key_exists("http_code", $response) && $response["http_code"] == "200") {
                $result = true;
            }
        } catch (Exception $e) {
            $result = false;
        }


        return $result;
    }


    function uninstall()
    {
        // get destructor SQL
        $this->loadDestructorSQL("Xowl.destructor.sql");
        parent::uninstall();
    }
}

