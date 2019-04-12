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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\NodeTypes;

use Ximdex\Models\Iterators\IteratorLinkDescriptions;
use Ximdex\Models\Link;
use Ximdex\Models\NodeDependencies;
use Ximdex\Models\RelLinkDescriptions;
use Ximdex\Logger;

/**
 * @brief Handles links to external pages or web sites
 */
class LinkNode extends Root
{
	public $link;

	public function __construct($parent = null)
	{
 		parent::__construct($parent);
		$this->link = new Link($this->nodeID);
	}

	/**
	 * Adds a row to Versions table and creates the file
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::createNode()
	 */
	public function createNode(string $name = null, int $parentID = null, int $nodeTypeID = null, int $stateID = null, string $url = null
	    , string $description = null)
	{
		$link = new Link();
		$link->set('IdLink', $this->nodeID);
		$link->set('Url', $url);
		$result = $this->parent->setDescription($description);
		$insertedId = $link->add();
		if (! $insertedId || ! $result) {
			$this->messages->add(_('The link could not be inserted'), MSG_TYPE_ERROR);
			$this->messages->mergeMessages($link->messages);
			return false;
		}
		$this->link = new Link($link->get('IdLink'));
		$relDescription = ! empty($description) ? $description : $this->link->getName();
		$rel = RelLinkDescriptions::create($this->nodeID, $relDescription);
		if (! $rel->getIdRel()) {
		    $this->messages->add(sprintf(_('Unable to create the description for link %s in its related table'), $link->get('IdLink'))
		        , MSG_TYPE_ERROR);
			$this->messages->mergeMessages($rel->messages);
			return false;
		}
		$this->updatePath();
		return $this->link->get('IdLink');
	}

	/**
	 * Deletes the information of link in the database
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::deleteNode()
	 */
	public function deleteNode() : bool
	{
		if (! $this->link->get('IdLink')) {
			Logger::error("Se ha solicitado eliminar el nodo {$this->nodeID} que actualmente no existe");
		}
		$result = $this->link->delete();
		if (! $result) {
			$this->parent->messages->add(_('Unable to remove link'), MSG_TYPE_ERROR);
			foreach ($this->link->messages->messages as $message) {
				$this->parent->messages[] = $message;
			}
		} else {
			$it = new IteratorLinkDescriptions('IdLink = %s', array($this->link->get('IdLink')));
			while ($rel = $it->next()) {
				if (! $rel->delete()) {
					Logger::warning(sprintf('No se ha podido eliminar la descripcion con id %s para el enlace %s.', $rel->getIdRel()
					    , $this->link->get('IdLink')));
				}
			}
		}
		return (bool) $result;
	}

	/**
	 * Gets the url of the link
	 * 
	 * @return boolean|string
	 */
	public function getUrl()
	{
		return $this->link->get('Url');
	}

	/**
	 * Sets the url of the link
	 * 
	 * @param string url
	 * @param bool commit
	 * @return bool
	 */
	public function setUrl(string $url, bool $commit = true)
	{
		$this->link->set('Url', $url);
		if ($commit) {
			$result = $this->link->update();
			if (! $result) {
				$this->parent->messages->add(_('Unable to remove link'), MSG_TYPE_ERROR);
				foreach ($this->link->messages->messages as $message) {
					$this->parent->messages[] = $message;
				}
			}
			return (bool) $result;
		}
		return true;
	}

	/**
	 * Gets the dependencies of the link
	 * 
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::getDependencies()
	 */
	public function getDependencies() : array
	{
		$nodeDependencies = new NodeDependencies();
		return $nodeDependencies->getByTarget($this->nodeID);
	}

	/**
	 * {@inheritDoc}
	 * @see \Ximdex\NodeTypes\Root::toXml()
	 */
	public function toXml(int $depth, array & $files, bool $recurrence = false)
	{
		$indexTabs = str_repeat("\t", $depth + 1);
		return sprintf("%s<LinkInfo Url=\"%s\">\n"
			. "%s\t<![CDATA[%s]]>\n"
			. "%s</LinkInfo>\n",
			$indexTabs, urlencode($this->link->get('Url')),
			$indexTabs, $this->parent->GetDescription(), $indexTabs);
	}
}
