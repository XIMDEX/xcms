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

ModulesManager::file('/inc/model/language.inc');
ModulesManager::file('/inc/model/IsoCode.class.php');
ModulesManager::file('/inc/helper/Messages.class.php');

class Action_createlanguage extends ActionAbstract {
	// Main method: shows initial form
    	function index() {
		$idNode = $this->request->getParam('nodeid');

        	$language = new Language();
        	$languages = $language->find('IsoName', '', NULL, MONO);

        	$isoCode = new IsoCode();
        	$isoCodes = $isoCode->find('Iso2', "1=1 order by IdIsoCode asc", NULL, MONO);
		$isoNames = $isoCode->find('Name',"1=1 order by IdIsoCode asc", NULL, MONO);

		if(!is_array($languages)) $languages = (array) $languages;

		$langs = array();
		if(!empty($isoCodes) ) {
			$i = 0;
			foreach ($isoCodes as $key => $isoCode) {
				if (!in_array($isoCode, $languages)) {
					$langs[$i] = array ( "code" => $isoCodes[$i], "name" =>  $isoNames[$i] );
				}
				$i++;
			}
		}

		$values = array(
			'id_node' => $idNode,
			'go_method' => 'createlanguage',
			'languages' => $langs);

		$this->render($values, null, 'default-3.0.tpl');
    	}

    	function createlanguage() {
 		$idNode = $this->request->getParam('nodeid');
 		$name = $this->request->getParam('langname');
 		$isoName = $this->request->getParam('isoname');
		$description = $this->request->getParam('description');
		$enabled = $this->request->getParam('enabled');

		$nodeType = new NodeType();
		$nodeType->SetByName('Language');

		$lang = new Node();
		$result = $lang->CreateNode($name, $idNode, $nodeType->get('IdNodeType'), null, $isoName, $description, $enabled);

		if ($result > 0) {
			$this->messages->add(_('Language has been succesfully added'), MSG_TYPE_NOTICE);
		}else {
			$this->messages->add(_("Language ").XmlBase::recodeSrc($name,$this->displayEncoding)._(" could not be ").XmlBase::recodeSrc(_('added'),$this->displayEncoding), MSG_TYPE_ERROR);
		}

		$this->reloadNode($idNode);

		$values = array('messages' =>  $this->messages->messages, 'idNode' => $idNode );
		$this->sendJSON($values);
    	}
}
?>