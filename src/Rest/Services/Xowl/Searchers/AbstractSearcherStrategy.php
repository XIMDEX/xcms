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

namespace Ximdex\Rest\Services\Xowl\Searchers;

/**
* Abstract class of strategy pattern for Xowl Searcher
* Use ximdex's rest provider to call an specific web service
* Abstract methods
*
* suggest
*/
abstract class AbstractSearcherStrategy
{
	protected $restProvider = null;
	protected static $XIMDEX_TYPE_DPERSON = "dPerson";
	protected static $XIMDEX_TYPE_DORGANISATION = "dOrganisation";
	protected static $XIMDEX_TYPE_DPLACE = "dPlace";
	protected static $XIMDEX_TYPE_DCREATIVEWORK = "dCreativeWork";
	protected static $XIMDEX_TYPE_DOTHERS = "dOthers";
	protected $data;
	
	public function __construct()
	{
		$this->restProvider = new \Ximdex\Rest\RESTProvider();
	}

	/**
	* Abstract method
	* Return related resources from a text
	*
	* @param $text : source to search
	* @return
	*/
	public abstract function suggest($text);

	/**
	* Get the loaded datas in several formats
	* 
	* @param $format : output format for data: array or json is waited
	* @return false if there aren't data. Json or array otherwise
	*/
	public final function getData($format = null)
	{
	    if (!$this->data) {
			return false;
	    }
		switch (strtolower($format)){			
			case "json":
				return json_encode($this->data);
			default:
				return $this->data;
		}			
	}

	/**
	* Get the loaded datas in several formats
	* 
	* @return false if there aren't data. Array otherwise
	*/
	public function getDataLikeArray()
	{
		return $this->getData();
	}

	/**
	* Get the loaded datas in several formats
	* 
	* @return false if there aren't data. Json otherwise
	*/
	public function getDataLikeJson()
	{
		return $this->getData("json");
	}
}