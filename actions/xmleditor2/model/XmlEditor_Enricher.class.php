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
 *  @version $Revision: 8043 $
 */




ModulesManager::file('/inc/fsutils/FsUtils.class.php');


class XmlEditor_Enricher {

	public static $config =  "/actions/xmleditor2/conf/schemaEnricher.xml";


	static function enrichSchema($content) {
    	$enricher = XIMDEX_ROOT_PATH . self::$config;

    	if (is_file($enricher) && !empty($content)) {
    		//Loading the RNG piece which should be included everywhere,
    		// we should obtain the name to include it in the RNG
    		$schemaPart = self::readConfig();
    		if ($schemaPart == NULL) {
    			XMD_Log::error(_('Error while loading the xml enricher'));
    		}

    		//Checking that it contains a 'define' tag which we need the name of
			$defineAttributeValue = self::readSchemaDefine();

    		//Loading the RNG to apply changes on it
    		$schemaDoc = new DomDocument();
    		$schemaDoc->loadXML($content);


    		$xpath = new DOMXpath($schemaDoc);
    		$xpath->registerNamespace('xmlns', 'http://relaxng.org/ns/structure/1.0');
			$elements = $xpath->query("//xmlns:element");

			if ($elements->length > 0) {

				for ($i = 0; $i < $elements->length; $i++) {
				$rngInclusion = $schemaDoc->createElement('zeroOrMore');
				$ref = $schemaDoc->createElement('ref');
				$ref->setAttribute('name', $defineAttributeValue);
					$rngInclusion->appendChild($ref);
					$element = $elements->item($i);
					$element->appendChild($rngInclusion);
				}
			}


			$schemaPart = self::readConfig();
    		//Importing the XML piece to the scheme as a 'define'
    		$importedNode = $schemaDoc->importNode(self::readSchemaDefineElement(), true);
    		$inclusionList = $schemaDoc->getElementsByTagName('grammar');
    		if ($inclusionList->length != 1) {
    			XMD_Log::error(_('Error while enriching the scheme, a RNG grammar label'));
    			return $content;
    		}
    		// We already have the label imported to the destiny RNG
    		$inclusionList->item(0)->appendChild($importedNode);

			$content = $schemaDoc->saveXML();
    	}

    	return $content;
    }

    public static function readConfig() {
    	$enricher = XIMDEX_ROOT_PATH . self::$config;
    	if (is_file($enricher)) {
    		//Loading the RNG piece that should be included everywhere
    		// We should obtain its name to include it in the RNG
    		$content = FsUtils::file_get_contents($enricher);
    		$schemaPart = new DomDocument();
    		$schemaPart->loadXML($content);

    		return $schemaPart;
    	}
    	return NULL;
    }

    public static function readSchemaDefine() {

    	return self::readElement(self::readConfig(), 'define');
    }

    public static function readSchemaDefineElement() {

    	return self::readElement(self::readConfig(), 'define', true);
    }

    public static function readSchemaElement() {
    	return self::readElement(self::readConfig(), 'element');
    }

    protected static function readElement($schema, $element, $returnElement = false) {
    	//Checking if a 'define' tag whome we need the name is contained
    	$nodeList = $schema->getElementsByTagName($element);
    	if ($nodeList->length != 1) {
    		XMD_Log::error(_("Error while enriching the scheme, a 'define' was not found in the configuration file"));
    		return $schema;
    	}

    	$defineElement = $nodeList->item(0);
    	if ($returnElement) {
    		return $defineElement;
    	}
    	$defineAttributeName = $defineElement->getAttributeNode('name');
    	$defineAttributeValue = $defineAttributeName->value;
    	return $defineAttributeValue;
    }

    protected static function readDefine($schema) {

    }
}
