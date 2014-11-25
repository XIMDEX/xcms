<?php

/**
 *  \details &copy; 2013  Open Ximdex Evolution SL [http://www.ximdex.org]
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

ModulesManager::file("/inc/model/nodetype.php");
require_once(XIMDEX_ROOT_PATH . '/inc/model/RelNodeTypeMetadata.class.php');

class NodetypeService
{
    const ROOT = 5001;
    const CONTROL_CENTER = 5002;
    const USER_MANAGER = 5003;
    const GROUP_MANAGER = 5004;
    const ROLE_MANAGER = 5005;
    const NODE_TYPE_MANAGER = 5006;
    const NODE_TYPE = 5007;
    const ACTION = 5008;
    const USER = 5009;
    const ROLE = 5010;
    const GROUP = 5011;
    const PROJECTS = 5012;
    const PROJECT = 5013;
    const SERVER = 5014;
    const SECTION = 5015;
    const IMAGES_ROOT_FOLDER = 5016;
    const IMAGES_FOLDER = 5017;
    const XML_ROOT_FOLDER = 5018;
    const IMPORT_ROOT_FOLDER = 5020;
    const IMPORT_FOLDER = 5021;
    const COMMON_ROOT_FOLDER = 5022;
    const COMMON_FOLDER = 5023;
    const CSS_ROOT_FOLDER = 5024;
    const CSS_FOLDER = 5025;
    const TEMPLATES_ROOT_FOLDER = 5026;
    const CSS_FILE = 5028;
    const CHANNEL_MANAGER = 5029;
    const LANGUAGE_MANAGER = 5030;
    const XML_CONTAINER = 5031;
    const XML_DOCUMENT = 5032;
    const CHANNEL = 5033;
    const LANGUAGE = 5034;
    const WORKFLOW_MANAGER = 5035;
    const WORKFLOW_STATE = 5036;
    const TEXT_FILE = 5039;
    const IMAGE_FILE = 5040;
    const BINARY_FILE = 5041;
    const ERROR_FOLDER = 5043;
    const TEMPLATE = 5044;
    const VISUAL_TEMPLATE = 5045;
    const LINK_FOLDER = 5048;
    const LINK = 5049;
    const LINK_MANAGER = 5050;
    const TEMPLATE_VIEW_FOLDER = 5053;
    const XIMLET_ROOT_FOLDER = 5054;
    const XIMLET_FOLDER = 5055;
    const XIMLET_CONTAINER = 5056;
    const XIMLET = 5057;
    const PROPERTIES_MANAGER = 5058;
    const PROPERTY = 5059;
    const PROJECT_PROP_FOLDER = 5060;
    const SYSTEM_PROPERTY = 5061;
    const PROP_SET = 5068;
    const NODE_HT = 5076;
    const XSL_TEMPLATE = 5077;
    const RNG_VISUAL_TEMPLATE = 5078;
    const WORKFLOW = 5079;
    const MODULES_FOLDER = 5080;
    const MODULE_INFO_CONTAINER = 5081;
    const INHERITABLE_PROPERTIES = 5082;
    const METADATA_SECTION = 5083;
    const METADATA_CONTAINER = 5084;
    const METADATA_DOCUMENT = 5085;


    public $nodeType;


    public function __construct($idNodeType)
    {
        if ($idNodeType)
            $this->nodeType = new Node($idNodeType);
    }

    /**
     * Check if the nodetype allow metadata.
     * @return boolean
     */
    public function isEnabledMetadata()
    {

        return RelNodeTypeMetadata::buildByIdNodeType($this->nodeType->get('IdNodeType')) ? true : false;
    }

    /**
     * Check if the nodetype must have metadata.
     * @return boolean
     */
    public function isMetadataForced()
    {

        $relNodeTypeMetadata = RelNodeTypeMetadata::buildByIdNodeType($this->nodeType->get('IdNodeType')) ? true : false;

        return $relNodeTypeMetadata ? $relNodeTypeMetadata->get("forced") : false;
    }
}
