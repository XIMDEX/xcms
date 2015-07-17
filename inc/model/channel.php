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

if (!defined('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__)) . '/../..');
}

require_once XIMDEX_ROOT_PATH . '/inc/model/orm/Channels_ORM.class.php';

class Channel extends Channels_ORM {
	/**
	 * Current channel identifier.
	 * @var unknown_type
	 */
	var $nodeID;
	/**
	 *
	 * @var unknown_type
	 */
	var $msgErr;
	/**
	 * Class error list.
	 * @var unknown_type
	 */
	var $errorList= array(
	1 => 'Database connection error',
	2 => 'Channel does not exist',
	3 => 'Database inconsistency'
	);

	/**
	 * Constructor
	 *
	 * @param int $nodeID
	 * @return Channel
	 */
	function Channel($nodeID = null)
	{
		$errorList[1] = _('Database connection error');
	        $errorList[2] = _('Channel does not exist');
	        $errorList[3] = _('Database inconsistency');

		parent::GenericData($nodeID);
	}

	/**
	 *
	 * @param $idNode
	 * @return unknown_type
	 */
	function getChannelsForNode($idNode) {

		$node = new Node($idNode);
		$channels = array();
		$channs = $node->getProperty('channel');

		if (!is_array($channs)) {
			// Inherits the system properties
			$channs = array();
			$systemChannels = $this->find('IdChannel');
			foreach ($systemChannels as $channel) {
				$channs[] = $channel['IdChannel'];
			}
		}

		if (count($channs) > 0) {
			foreach ($channs as $channelId) {
				$channel = new Channel($channelId);
				$channels[] = array(
					'IdChannel' => $channelId,
					'Name' => $channel->get('Name'),
					'Description' => $channel->get('Description')
				);
			}
		}

		return count($channels) > 0 ? $channels : null;

	}
	/**
	 * Returns all the channels (it should go in an iterator).
	 *
	 * @return array
	 */
	function GetAllChannels($order = NULL)
	{
		$validDirs = array('ASC', 'DESC');
		$sql = "SELECT IdChannel FROM Channels";
		if (!empty($order) && is_array($order) && isset($order['FIELD'])) {
			$sql .= sprintf(" ORDER BY %s %s", $order['FIELD'],
			isset($order['DIR']) && in_array($order['DIR'], $validDirs) ? $order['DIR'] : '');
		}

		$dbObj = new DB();
		$dbObj->Query($sql);
		if ($dbObj->numErr != 0)
		{
			$this->SetError(1);
			return null;
		}
		while (!$dbObj->EOF)
		{
			$salida[] = $dbObj->GetValue("IdChannel");
			$dbObj->Next();
		}

		return $salida ? $salida : NULL;
	}

	/**
	 * Returns the channel id.
	 *
	 * @return int/bool
	 */
	function GetID()
	{
		if ($this->get('IdChannel') > 0) {
			return $this->get('IdChannel');
		} else {
			$this->SetError(2);
			return false;
		}
	}

	/**
	 * Load a channel with a given id //TODO try to delete this functions in the code
	 *
	 * @param int $nodeID
	 * @return int/bool
	 */
	function SetID($nodeID)
	{
		parent::GenericData($nodeID);
		if (!($this->IdChannel > 0)) {
			$this->SetError(2);
			return null;
		}
		return $this->IdChannel;
	}

	/**
	 * Changes the current channel id.
	 * @param $name
	 * @return unknown_type
	 */
	function SetByName($name)
	{
		$dbObj = new DB();
		$dbObj->Query(sprintf("SELECT IdChannel FROM Channels WHERE Name='%s'", $this->dbObj->sqlEscapeString($name)));
		parent::GenericData($this->GetValue('IdChannel'));

		if (!($this->IdChannel > 0)) {
			$this->SetError(2);
			return null;
		}
		return $this->IdChannel;
	}

	/**
	 * Returns the current channel name.
	 * @return string(name)
	 */
	function GetName(){
		return $this->get('Name');
	}
	/**
	 * Changes the current channel name.
	 * @param $name
	 * @return int (status)
	 */
	function SetName($name)
	{
		if (!($this->get('IdChannel') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('Name', $name);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Returns the current channel description.
	 * @return string (description)
	 */
	function GetDescription ()
	{
		return $this->get('Description');
	}

	/**
	 * Changes the current channel description.
	 * @param $description
	 * @return int (status)
	 */
	function SetDescription($description)
	{
		if (!($this->get('IdChannel') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('Description', $description);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	* Returns the current channel extension by default
	* @return string (description)
	*/
	function GetExtension(){
		return $this->get('DefaultExtension');
	}
	/**
	 * Changes the default extension for current channel.
	 * @param $ext
	 * @return int (status)
	 */
	function SetExtension($ext)
	{
		if (!($this->get('IdChannel') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('DefaultExtension', $ext);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetFormat()
	{
		return $this->get('Format');
	}

	/**
	 * Changes the default format for the current channel.
	 * @param $format
	 * @return int (status)
	 */
	function SetFormat($format)
	{
		if (!($this->get('IdChannel') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('Format', $format);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 * Creates a new channel and loads its id in the class docID.
	 * @param $name
	 * @param $defaultExtension
	 * @param $format
	 * @param $description
	 * @param $idChannel
	 * @param $filter
	 * @param $renderMode
	 * @param $outputType
	 * @return channelID - loaded as a attribute.
	 */
	function CreateNewChannel($name, $defaultExtension, $format, $description, $idChannel, $filter = "",$renderMode="", $outputType="")
	{
		$this->set('IdChannel', (int) $idChannel);
		$this->set('Name', $name);
		$this->set('Description', $description);
		$this->set('DefaultExtension', $defaultExtension);
		$this->set('Format', $format);
		$this->set('Filter', $filter);		
		$this->set('RenderMode', $renderMode);
		$this->set('OutputType', $outputType);
        $this->set('Default_Channel', 0);
		if ($this->add()) {
			return $this->get('IdChannel');
		}
		return false;
	}

	/**
	 * Deletes current channel
	 * @return int (status)
	 */
	function DeleteChannel()
	{
		if (!($this->get('IdChannel') > 0)) {
			$this->SetError(2);
			return false;
		}

		return $this->delete();
	}

	/**
	 *
	 * @return unknown_type
	 */
	function GetFilter(){
		return $this->get('Filter');
	}

	/**
	 *
	 * @param $filter
	 * @return unknown_type
	 */
	function SetFilter($filter){
		if (!($this->get('IdChannel') > 0)) {
			$this->SetError(2);
			return false;
		}

		$result = $this->set('Filter', $filter);
		if ($result) {
			return $this->update();
		}
		return false;
	}

	/**
	 *
	 * @return unknown_type
	 */
	function ClearError() {
		$this->flagErr = FALSE;
	}
	/**
	 * Loads a class error
	 * @param $code
	 * @return unknown_type
	 */
	function SetError($code) {
		$this->flagErr = TRUE;
		$this->numErr = $code;
		$this->msgErr = $this->errorList[$code];
	}

	/**
	 * Returns true if there was an error in the class.
	 * @return unknown_type
	 */
	function HasError() {
		$aux = $this->flagErr;
		if ($this->autoCleanErr)
		$this->ClearError();
		return $aux;
	}

    /**
     * Set property Default_Channel to 0 for all
     * Channels except the channel with id $idchannel
     */
    function setDefaultChannelToZero($idchannel){
        $dbObj = new DB();
        return $dbObj->Execute(sprintf("UPDATE Channels SET Default_Channel=0 WHERE IdChannel<>%s;",$idchannel));
    }
}
