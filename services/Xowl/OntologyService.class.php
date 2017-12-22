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

use Ximdex\Runtime\App;

ModulesManager::file('/services/Xowl/searchers/AnnotationSearcherStrategy.class.php');
ModulesManager::file('/services/Xowl/searchers/ContentEnricherSearcherStrategy.class.php');
ModulesManager::file('/services/Xowl/searchers/ExternalVocabularySearcherStrategy.class.php');
ModulesManager::file('/services/Xowl/searchers/XimdexSearcherStrategy.class.php');


/**
 * Class OntologyService
 */
class OntologyService
{

    /**
     * @var array
     */
    private static $allProviders = array(
        "semantic" => "AnnotationSearcherStrategy",
        "content" => "ContentEnricherSearcherStrategy",
        "external" => "ExternalVocabularySearcherStrategy"
    );
    /**
     * @var bool
     */
    private $key = false;
    /**
     * @var array
     *
     */
    private $providers = array();

    /**
     *Class constructor. Check if Xowl module is enabled and if exists EnricherKey;
     *It can receive a variable number of params with the name of the providers to load.
     */
    public function __construct()
    {

        //Loading all defined providers

        $this->loadProviders(func_get_args());

        if (ModulesManager::isEnabled('Xowl')) {
            $key = App::getValue('Xowl_token');

            if ($key !== NULL && $key != '') {
                $this->key = $key;
            }


        }
    }

    /**
     * Load all providers and update the provider property
     * It can receive an array of strings with the name of the providers to load.
     * Load all providers is there aren't params or if the array is empty.
     */
    private function loadProviders($providers = null)
    {

        //It should be loaded from DB.
        if ($providers && count($providers)) {
            foreach ($providers as $provider) {
                if ($provider) {
                    if (array_key_exists($provider, self::$allProviders)) {
                        $this->providers[$provider] = self::$allProviders[$provider];
                    }
                }
            }
        } else {
            $this->providers = self::$allProviders;
        }
        return $this;
    }

    /**
     * <p>Load all existing namespaces in this ximdex instance.</p>
     * @return Namespaces[] with all namespaces in Namespaces table.
     */
    public static function getAllNamespaces()
    {
        $namespace = new \Ximdex\Models\Namespaces();
        return $namespace->getAll();
    }

    /**
     *Suggest related terms and resources from a text
     *If key exists return semantic and suggested terms.
     *
     */
    /**
     * @param $text
     * @param  null|Provider[]
     * @return array|bool
     */
    public function suggest($text)
    {

        if ($this->key) {
            $result = array();
            foreach ($this->providers as $type => $providerName) {
                if (class_exists($providerName)) {
                    /**
                     * @var $provider AbstractSearcherStrategy
                     */
                    $provider = new $providerName;

                    $result[$type] = $provider->suggest($text)->getData();
                }
            }

            $result["status"] = "ok";
            return $result;
        }
        return false;
    }
}