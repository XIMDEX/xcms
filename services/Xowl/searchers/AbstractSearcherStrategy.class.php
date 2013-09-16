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

ModulesManager::file('/inc/rest/REST_Provider.class.php');
/**
*<p>Abstract class of strategy pattern for Xowl Searcher</p>
*<p>Use ximdex's rest provider to call an specific web service</p>
*<p>Abstract methods</p>
*<ul>
* <li>suggest</li>
*</ul>
*/
abstract class AbstractSearcherStrategy {

	protected $restProvider = null;
	protected static $XIMDEX_TYPE_DPERSON = "dPerson";
	protected static $XIMDEX_TYPE_DORGANISATION = "dOrganisation";
	protected static $XIMDEX_TYPE_DPLACE = "dPlace";
	protected $data;
	
	public function __construct(){		
		$this->restProvider = new REST_Provider();
	}

	/**
	*<p>Abstract method<p>
	*<p>Return related resources from a text</p>
	*<p>@param $text source to search.</p>
	*<p>@return</p>
	*/

	public abstract function suggest($text);

	/**
	*<p>Get the loaded datas in several formats</p>
	*@param $format output format for data: array or json is waited.
	*@return false if there aren't data. Json or array otherwise.
	*/
	public final function getData($format = null){

		if (!$this->data)
			return false;
		switch (strtolower($format)){			
			case "json":
				return json_encode($this->data);
			case "array":
			default:
				return $this->data;
		}			
	}

	/**
	*<p>Get the loaded datas in several formats</p>
	*@return false if there aren't data. Array otherwise.
	*/
	public function getDataLikeArray(){

		return $this->getData();
	}

	/**
	*<p>Get the loaded datas in several formats</p>
	*@return false if there aren't data. Json otherwise.
	*/
	public function getDataLikeJson(){
		
		return $this->getData("json");
	}
	

}

?>
