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
 *  @version $Revision: 8178 $
 */



ModulesManager::file('/inc/nodetypes/xsltnode.inc');

class HTML2XML {

	private $_domXml = null;
	private $_xpath = null;
	private $_domHtml = null;
	private $_ximNode = null;
	private $_uidMap = array();
	private $_uidMapHtml = array();

	private $_tagCounter = 0;
	private $_maxTagCounter = 0;
	private $_xmlContent = '';

	/**
	 * Function which returns the DOMDocument object corresponding to the XML
	 * @return object
	 */
	public function getXML() {

		return $this->_domXml;
	}

	/**
	 * Function which returns the XML content
	 * @return object
	 */
	public function getXmlContent() {

		return $this->_xmlContent;
	}

	/**
	 * Function which returns the DOMDocument object corresponding to the HTML
	 * @return object
	 */
	public function getHTML() {

		return $this->_domHtml;
	}

	/**
	 * Function which loads the DOMDocument object corresponding to the XML
	 * @return object
	 */
	public function loadXML($domXml) {

		$this->_domXml = $domXml;
	}

	/**
	 * Function which loads the DOMDocument object corresponding to the XML
	 * @return object
	 */
	public function loadHTML($domHtml) {

		$this->_domHtml = $domHtml;
	}

	/**
	 * Function which stablishes the referenced node (ximdex)
	 * @return object
	 */
	public function setXimNode($idNode) {

		$this->_ximNode = $idNode;
	}

	/**
	 * Function which performs the HTML transformation
	 * @return boolean
	 */
	public function transform() {

		// Assinging Uid to XML
		$this->assignUidToXml();

		// Assinging Uid 1 to Body
		$this->assignUidToBody();

		// Translating Html changes to Xml
		$this->applyChanges();

		// Obtaining xmlContent excluding docxap
		$docxap = $this->_domXml->getElementsByTagName('docxap');
		$docxap = $docxap->item(0);
		unset($this->_xpath);
		$this->_xpath = new DOMXPath($this->_domXml);
		$nodeList = $this->_xpath->query('/docxap/*');
		$this->_xmlContent = '';
		foreach ($nodeList as $child) {
			if ($child && $child->nodeType == 1) {
				$this->_xmlContent .= $this->_domXml->saveXML($child) . "\n";
			}
		}

		return true;
	}

	private function assignUidToXml() {

		$this->_xpath = new DOMXPath($this->getXML());
		$nodeList = $this->_xpath->query('//*');

		$this->_tagCounter = 0;

		// Adding attribute uid to each node.
		foreach ($nodeList as $child) {
			if ($child && $child->nodeType == 1) {
				$this->_tagCounter ++;
				$this->_uidMap[$this->_ximNode . "." . $this->_tagCounter] = $child;
			}
		}

		$this->_maxTagCounter = $this->_tagCounter;
		unset($this->_xpath);
	}

	private function assignUidToBody() {

		// For now, atribute uid=1 will be set up for the body. As soon as kupu would be touched, it will add it.
		$this->_xpath = new DOMXPath($this->_domHtml);
		$nodeList = $this->_xpath->query('//body');
		$nodeList->item(0)->setAttribute('uid', $this->_ximNode . ".1");

		unset($this->_xpath);
	}

	private function getTextContent ($node = null) {

		if ($cn = $node->firstChild) {
			while ($cn) {
				if ($cn->nodeType == XML_TEXT_NODE) {
					return $cn->nodeValue;
   				}
				$cn = $cn->nextSibling;
			}
		}

		return false;
	}

	private function setTextContent ($node = null, $content = '') {

		if ($cn = $node->firstChild) {
			while ($cn) {
				if ($cn->nodeType == XML_TEXT_NODE) {
					$cn->replaceData(0, $cn->length, $content);
					return true;
   				}
				$cn = $cn->nextSibling;
			}
		}

		$node->appendChild(new DOMText($content));
		return true;
	}

	private function getParentNodeWithUid ($node, &$parentNode) {

		// Retrieving parent Node with set uid
		XMD_Log::info(_('Getting parent node with set uid...'));
		$parentUid = null;
		while($parentNode = $node->parentNode) {
			if(get_class($parentNode) == "DOMElement" && $parentUid = $parentNode->getAttribute('uid')) {
				XMD_Log::info(_('Parent Node retrieved').' (' . $parentNode->nodeName . ')  '._('with uid = ' ). $parentUid);
				break;
			}
			$node = $parentNode;
		}

		return $parentUid;
	}

	private function applyChanges() {

		$this->_xpath = new DOMXPath($this->_domHtml);
		$nodeList = $this->_xpath->query('//*');
		$tagCounter = 0;
		$lastUid = 0;

		// Searching for edited nodes (uid set) and new nodes (uidtype) in html
		XMD_Log::info(_('Searching for edited nodes (uid set) and new nodes (uidtype) in html'));
		foreach ($nodeList as $idChild => $child) {

			if ($child && $child->nodeType == 1) {

				// Getting uid
				$tagCounter = $child->getAttribute('uid');

				XMD_Log::info(_('Node').': ' . $child->nodeName);
				if (in_array($tagCounter, array_keys($this->_uidMap))) {

					// Uid exists. Editting content in XML
					XMD_Log::info('uid (' . $tagCounter . ') '._('exists. Editing content...'));
					$newChild = $this->_uidMap[$tagCounter];
					if($textContent = $this->getTextContent($child)) {
						$this->setTextContent($newChild, $textContent);
					}
					$lastUid = $tagCounter;

					// Adding to uidMapHtml array
					$this->_uidMapHtml[] = $tagCounter;
				} else {

					// Non-existing uid. Getting Type
					XMD_Log::info(_('Non-existing uid. Getting type...'));
					$type = $child->getAttribute('uidtype');
					if($type) {
						XMD_Log::info(_('Type retrieved: ') . $type);
					} else {
						XMD_Log::info(_('Type retrieved: none :: No translation applied'));
						continue;
					}

					// Retrieving parent Node with set uid
					$parentNode = null;
					if($parentUid = $this->getParentNodeWithUid($child, $parentNode)) {

						XMD_Log::info(_('Parent node retrieved: ') . $parentNode->nodeName);
						XMD_Log::info(_('Parent Uid retrieved: ') . $parentUid);
						XMD_Log::info(_('Appending new child to parent node in XML document...'));

						// Appending new child to parent Node in XML document
						$parent = $this->_uidMap[$parentNode->getAttribute('uid')];
						// Creating, adding text content and appending new node to xml document
						$newChild = $this->_domXml->createElement($type);
						if($textContent = $this->getTextContent($child)) {
							$this->setTextContent($newChild, $textContent);
						}

						// Assigning new uid to actual node
						$lastUid ++;
						XMD_Log::info(_('Setting uid ' . $lastUid . ' to ') . $child->nodeName);
						$child->setAttribute('uid', $this->_ximNode . "." . $lastUid);

						// Appending
						XMD_Log::info(_('Appending ' . $newChild->nodeName . ' to ' . $parent->nodeName . ' before node with uid ') . $lastUid);
						if($nextSiblingUid = $this->getNextSiblingUid($child)) {
							$nextSibling = $this->_uidMap[$nextSiblingUid];
						} else {
							$nextSibling = null;
						}
						$parent->insertBefore($newChild, $nextSibling);

						// Incrementing uid to next element in array
						$this->incrementUidFrom($lastUid);

						// Assigning same uid to original XML
						$this->_uidMap[$this->_ximNode . "." . $lastUid] = $newChild;

						// Adding to uidMapHtml array
						$this->_uidMapHtml[] = $this->_ximNode . "." . $lastUid;
					}
				}
			}
		}

		// Searching for deleted elements
		XMD_Log::info(_('Searching for deleted elements...'));
		$this->deleteXmlElements();

	}

	private function incrementUidFrom($from) {

		// Incrementing uid from $from to last element with defined uid
		$auxMap = $this->_uidMap;
		$xpath = new DOMXPath($this->_domHtml);
		$nodeList = $xpath->query('//*[@uid]');
		foreach ($nodeList as $idChild => $child) {
			$uidValue = $this->getUidValueFromAttribute($child->getAttribute('uid'));
			if($uidValue >= $from) {
				$child->setAttribute('uid', $this->_ximNode . "." . ($uidValue + 1));
				$this->_uidMap[$this->_ximNode . "." . ($uidValue + 1)] = $auxMap[$this->_ximNode . "." . $uidValue];
			}
		}
	}

	private function getUidValueFromAttribute($attributeContent) {

		$value = intval(str_replace($this->_ximNode . ".", "", $attributeContent));
		return (is_int($value)) ? $value : null;
	}

	private function deleteXmlElements() {

		foreach($this->_uidMap as $uid => $node) {
			if(!in_array($uid, $this->_uidMapHtml)) {
				XMD_Log::info(_('Deleting element with uid ') . $uid);
				$node->parentNode->removeChild($node);
				unset($this->_uidMap[$uid]);
			}
		}

		return true;
	}

	private function getNextSiblingUid ($node) {

		while($node = $node->nextSibling) {
			if($node->nodeType == 1) {
				return $node->getAttribute('uid');
			}
		}

		return null;
	}

	public function getNodeWithUID($uid) {
		$node = isset($this->_uidMap[$uid]) ? $this->_uidMap[$uid] : null;
		return $node;
	}

}

?>
