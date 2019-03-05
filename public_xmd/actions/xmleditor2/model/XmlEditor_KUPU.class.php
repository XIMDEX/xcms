<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
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

use Ximdex\Deps\DepsManager;
use Ximdex\Models\Channel;
use Ximdex\Models\Language;
use Ximdex\Models\Node;
use Ximdex\Models\RelSemanticTagsNodes;
use Ximdex\Models\User;
use Ximdex\Models\Transition;
use Ximdex\Parsers\ParsingJsGetText;
use Ximdex\Parsers\ParsingRng;
use Ximdex\Parsers\ParsingXsl;
use Ximdex\Runtime\App;
use Ximdex\Runtime\DataFactory;
use Ximdex\Utils\FsUtils;
use Ximdex\Logger;
use Ximdex\Sync\SynchroFacade;
use Ximdex\Nodeviews\ViewPreviewInServer;

\Ximdex\Modules\Manager::file('/actions/xmleditor2/model/XmlEditor_Abstract.class.php');
\Ximdex\Modules\Manager::file('/actions/xmleditor2/HTML2XML.class.php');
\Ximdex\Modules\Manager::file('/actions/enricher/model/Enricher.class.php', 'Xowl');
\Ximdex\Modules\Manager::file('/actions/enricher/model/TagSuggester.class.php', 'Xowl');
\Ximdex\Modules\Manager::file('/actions/xmleditor2/model/XmlEditor_Enricher.class.php');

class XmlEditor_KUPU extends XmlEditor_Abstract
{
    private $domDoc = null;
    private $node = null;
    private $view = null;

    public function getEditorName()
    {
        return $this->_editorName;
    }

    public function getBaseURL()
    {
        return $this->_base_url;
    }

    public function setBaseURL($base_url)
    {
        $this->_base_url = $base_url;
    }

    public function setEditorName($editorName)
    {
        $this->_editorName = $editorName;
    }

    public function openEditor($idnode, $view)
    {
        $node = new Node($idnode);
        if (!($node->get('IdNode') > 0)) {
            Logger::error(_("A non-existing node cannot be obtained: ") . $node->get('IdNode'));
            return null;
        }
        $channels = $node->getChannels();
        $channelList = array();
        if ($channels) {
            foreach ($channels as $idChannel) {
                $channel = new Channel($idChannel);
                $channelObj = array();
                $channelObj["text"] = _('Preview as') . ' ' . $channel->getName();
                $channelObj["href"] = "#";
                $channelObj["data"] = $idChannel;
                $channelList[] = $channelObj;
            }
        }
        $availableViews = array('tree');
        if (!$this->getXslFile($idnode, $view) || count($channelList) == 0) {
            $view = 'tree';
        } else {
            $availableViews[] = 'normal';
        }
        if ((boolean)App::getValue('PreviewInServer') && (boolean)count($channelList)) {
            $availableViews[] = 'pro';
        }
        $nodeTypeName = $node->nodeType->GetName();
        $rngEditorMode = ($nodeTypeName == 'RngVisualTemplate') ? true : false;
        $dotdotPath = null;
        if ($rngEditorMode === false) {
            $depth = $node->GetPublishedDepth();
            $dotdot = str_repeat('../', $depth - 2);
            $section = new Node($node->GetSection());
            $sectionPath = $section->class->GetNodeURL() . "/";
            $dotdotPath = $sectionPath . $dotdot;
        }
        $xmlFile = $this->_base_url . '&method=getXmlFile&view=' . $view;
        $actionUrlShowPost = $this->_base_url . '&method=showPost';
        $actionURL = App::getUrl('/actions/xmleditor2', false);
        $vendorsURL = App::getUrl('/vendors', false);
        $kupuURL = $vendorsURL . '/kupu';
        $jsFiles = array(
            App::getUrl(Extensions::JQUERY, false),
            App::getUrl(Extensions::JQUERY_UI, false),
            App::getUrl(Extensions::JQUERY_PATH . '/js/fix.jquery.getters.js', false),
            App::getUrl(Extensions::JQUERY_PATH . '/js/fix.jquery.parsejson.js', false),
            App::getUrl(Extensions::JQUERY_PATH . '/plugins/jquery.json/jquery.json-2.2.min.js', false),
            App::getUrl(Extensions::BOOTSTRAP . '/js/bootstrap.min.js', false),
            App::getUrl('/assets/js/helpers.js', false),
            App::getUrl('/assets/js/sess.js', false),
            App::getUrl('/assets/js/collection.js', false),
            App::getUrl('/assets/js/dialogs.js', false),
            App::getUrl('/assets/js/ximtimer.js', false),
            App::getUrl('/assets/js/console.js', false),
            App::getUrl('/src/Widgets/select/js/ximdex.select.js', false),
            App::getUrl('/assets/js/i18n.js', false),
            $vendorsURL . '/hammerjs/hammer.js/hammer.js',
            $vendorsURL . '/angular/angular.min.js',
            $vendorsURL . '/RyanMullins/angular-hammer/angular.hammer.js',
            $vendorsURL . '/angular-bootstrap/dist/ui-bootstrap-custom-tpls-0.13.0-SNAPSHOT.min.js',
            $vendorsURL . '/react/react-with-addons.min.js',
            $vendorsURL . '/react/ngReact.min.js',
            $actionURL . '/js/angular/app.js',
            $actionURL . '/js/angular/ximOntologyBrowser.js',
            $vendorsURL . '/d3js/d3.v3.min.js',
            //'/assets/js/angular/app.js',
            App::getUrl('/assets/js/angular/services/xTranslate.js', false), 
            App::getUrl('/assets/js/angular/services/xBackend.js', false),
            App::getUrl('/assets/js/angular/services/xUrlHelper.js', false),
            App::getUrl('/assets/js/angular/services/xTranslate.js', false),
            App::getUrl('/assets/js/angular/services/xMenu.js', false),
            App::getUrl('/assets/js/angular/services/angularLoad.js', false),
            App::getUrl('/assets/js/angular/services/xTabs.js', false),
            App::getUrl('/assets/js/angular/directives/ximButton.js', false),
            App::getUrl('/assets/js/angular/directives/ximSelect.js', false),
            App::getUrl('/assets/js/angular/directives/ximMenu.js', false),
            App::getUrl('/assets/js/angular/directives/ximBrowser.js', false),
            App::getUrl('/assets/js/angular/directives/ximValidators.js', false),
            App::getUrl('/assets/js/angular/filters/xFilters.js', false),
            App::getUrl('/assets/js/angular/directives/ximButton.js', false),
            App::getUrl('/assets/js/angular/directives/xtagsSuggested.js', false),
            App::getUrl('/assets/js/angular/directives/ximTree.js', false),
            App::getUrl('/assets/js/angular/directives/treeNode.jsx.js', false),
            App::getUrl('/assets/js/angular/controllers/XTagsCtrl.js', false),
            App::getUrl('/assets/js/angular/controllers/SearchTreeModal.js', false),
            $kupuURL . '/common/sarissa.js',
            $kupuURL . '/common/sarissa_ieemu_xpath.js',
            $kupuURL . '/common/kupuhelpers.js',
            $kupuURL . '/common/kupubasetools.js',
            $kupuURL . '/common/kupuloggers.js',
            // $kupuURL . '/common/kupunoi18n.js',
            $kupuURL . '/i18n/js/i18n.js',
            $kupuURL . '/common/kupucleanupexpressions.js',
            $kupuURL . '/common/kupucontentfilters.js',
            $kupuURL . '/common/kuputoolcollapser.js',
            $kupuURL . '/common/kupueditor.js',
            $kupuURL . '/common/kupusourceedit.js',
            $kupuURL . '/common/kupuspellchecker.js',
            $kupuURL . '/common/kupudrawers.js',

            /* ####### HELPERS ########## */
            $actionURL . '/js/helpers/loadingImage.js',
            $actionURL . '/js/helpers/xsltParser.js',
            $actionURL . '/js/helpers/helpers.js',
            $actionURL . '/js/helpers/autoscrolling.js',
            $actionURL . '/js/helpers/EditorHandlers_Adapter.js',
            $actionURL . '/js/helpers/DOMNodeIterator.class.js',
            // Future $actionURL . '/js/helpers/colorpicker.js',
            $actionURL . '/js/helpers/DOMAttrIterator.class.js',

            /* ####### DOM ########## */
            $actionURL . '/js/dom/XimDocument.class.js',
            $actionURL . '/js/dom/XimElement.class.js',
            $actionURL . '/js/dom/RngDocument.class.js',
            $actionURL . '/js/dom/RngElement.class.js',

            /* ####### EDITOR ########## */
            $actionURL . '/js/editor/kupucontextmenu.js',
            $actionURL . '/js/editor/ximdexeditor.js',
            $actionURL . '/js/editor/initKupuTools.js',
            $actionURL . '/js/editor/kupu_form.js',
            $actionURL . '/js/editor/kupu_start.js',
            $actionURL . '/js/editor/BrowserCompatibility.class.js',

            /* ####### BASE ########## */
            $actionURL . '/js/tools/XimdocTool.js',
            $actionURL . '/js/buttons/XimdocButton.js',
            $actionURL . '/js/toolboxes/XimdocToolBox.class.js',
            $actionURL . '/js/tools/XimdocContextMenuTool.class.js',

            /* ###Especial ToolBox### */
            $actionURL . '/js/toolboxes/FloatToolbarToolBox.class.js',

            /* ####### TOOLS ########## */
            $actionURL . '/js/tools/XimdocEditableContentTool.class.js',
            $actionURL . '/js/tools/HoverTool.class.js',
            $actionURL . '/js/tools/EditorViewTool.class.js',
            $actionURL . '/js/tools/XimdocSpellCheckerTool.class.js',
            $actionURL . '/js/tools/XimdocAnnotationTool.class.js',
            $actionURL . '/js/tools/XimdocPreviewTool.class.js',
            $actionURL . '/js/tools/ximletTool.class.js',
            $actionURL . '/js/tools/StructuredListTool.class.js',
            $actionURL . '/js/tools/AttributesTool.js',
            $actionURL . '/js/tools/XimdocAnnotationRdfaTool.class.js',
            $actionURL . '/js/tools/ToolbarTool.js',
            $actionURL . '/js/tools/ImagesTool.js',
            $actionURL . '/js/tools/RMXimdexTool.js',
            $actionURL . '/js/tools/NavBarTool.js',

            /* ####### DRAWERS ########## */
            $actionURL . '/js/drawers/XimdocDrawerTool.js',
            $actionURL . '/js/drawers/TableWizardDrawer.class.js',
            $actionURL . '/js/drawers/XimletDrawer.class.js',
            $actionURL . '/js/drawers/XimlinkDrawer.class.js',
            $actionURL . '/js/drawers/XimimageDrawer.class.js',
            $actionURL . '/js/drawers/DateDrawer.class.js',

            /* ####### TOOLBOXES ########## */
            $actionURL . '/js/toolboxes/ToolbarButtonsToolBox.js',
            $actionURL . '/js/toolboxes/HighlightToolBox.class.js',
            $actionURL . '/js/toolboxes/DraggablesToolBox.class.js',
            $actionURL . '/js/toolboxes/ToolContainerToolBox.class.js',
            $actionURL . '/js/toolboxes/FloatingToolBox.class.js',
            $actionURL . '/js/toolboxes/XimdexLogger.class.js',
            $actionURL . '/js/toolboxes/InfoToolBox.class.js',
            $actionURL . '/js/toolboxes/AttributesToolBox.class.js',
            // $actionURL . '/js/toolboxes/ChannelsToolBox.class.js',
            $actionURL . '/js/toolboxes/ChangesetToolBox.class.js',
            $actionURL . '/js/toolboxes/AnnotationToolBox.class.js',
            $actionURL . '/js/toolboxes/AnnotationRdfaToolBox.class.js',
            $actionURL . '/js/toolboxes/RNGElementsToolBox.class.js',
            $actionURL . '/js/toolboxes/NavBarToolBox.class.js',
			// $actionURL . '/js/editor/ToolbarToolBox.class.js',

            /* ##### buttons #### */
            $actionURL . '/js/buttons/SchemaValidatorButton.js',
            $actionURL . '/js/buttons/ToggleButton.js',
            $actionURL . '/js/buttons/XimdocRemoveElementButton.js',
            $actionURL . '/js/buttons/XimdocRngElementButton.js',

            /* ####### widgets  #### */
            $actionURL . '/js/widgets/lib/treeview/datasource.js',
            $actionURL . '/js/widgets/lib/treeview/treeview.js'
        );

        $i18n = new ParsingJsGetText();
        $jsFiles = $i18n->getTextArrayOfJs($jsFiles);
        $actionURL =  App::getUrl('/actions/xmleditor2');
        $kupuURL = App::getUrl('/vendors/kupu');
        $cssFiles = array(
            App::getUrl('/assets/style/jquery/custom_theme/jquery-ui-1.7.custom.css'),
            $actionURL . '/views/common/css/kupustyles.css',
            $actionURL . '/views/common/css/toolboxes.css',
            $actionURL . '/views/common/css/treeview.css',
            $kupuURL . '/common/kupudrawerstyles.css',
            $actionURL . '/views/common/css/xlinks.css',
            $actionURL . '/views/common/css/ximages.css',
            $actionURL . '/views/common/css/popover.css',
            App::getUrl( '/vendors/bootstrap/dist/css/bootstrap.min.css'),
            // Future		$actionURL . '/views/common/css/colorpicker.css',
            App::getUrl('/assets/style/fonts.css'),
            App::getUrl('/assets/style/jquery/ximdex_theme/widgets/tabs/common_views.css'),
            App::getUrl('/src/Widgets/select/css/ximdex.select.css'),
            App::getUrl('/assets/style/jquery/ximdex_theme/widgets/treeview/treeview.css'),
            App::getUrl('/assets/style/jquery/ximdex_theme/widgets/tagsinput/tagsinput_editor.css'),
        );
        $baseTags = array(
            $kupuURL . '/common/'
        );
        $options = array(
            sprintf("editor_view: '%s'", $view),
            sprintf("rngEditorMode: '%s'", $rngEditorMode),
            sprintf("dotdotPath: '%s'", $dotdotPath),
            sprintf("availableViews: ['%s']", implode("','", $availableViews)),
        );
        $namespaces = json_encode($this->getAllNamespaces());
        $relTags = new RelSemanticTagsNodes();
        $tags = str_replace("'", '&#39;', json_encode($relTags->getTags($idnode), JSON_UNESCAPED_UNICODE));
        $onloadfunctions = sprintf("kupu = startKupu({%s});", implode(", ", $options));
        $values = array('nodeid' => $idnode,
            'xmlFile' => $xmlFile,
            'actionUrlShowPost' => $actionUrlShowPost,
            'rngEditorMode' => $rngEditorMode,
            'dotdotPath' => $dotdotPath,
            'js_files' => $jsFiles,
            'css_files' => $cssFiles,
            'base_tags' => $baseTags,
            'tags' => $tags,
            'namespaces' => $namespaces,
            'channels' => json_encode($channelList),
            'on_load_functions' => $onloadfunctions
        );
        return $values;
    }

    public function getConfig($idNode)
    {
        if (!$this->setNode($idNode)) {
            Logger::error(_("A non-existing node cannot be edited: ") . $idNode);
        }
        $user = new \Ximdex\Models\User(\Ximdex\Runtime\Session::get('userID'));
        $hasPermission = $user->hasPermission('expert_mode_allowed');
        $expert_mode_allowed = $hasPermission ? '1' : '0';
        $canPublicate = $this->canPublicate($idNode);
        $publication_allowed = $canPublicate ? '1' : '0';
        $checkSpelling = in_array("enchant", get_loaded_extensions()) ? "1" : "0";
        $xmlFile = $this->_base_url . '&method=getXmlFile';
        $content = FsUtils::file_get_contents(APP_ROOT_PATH . '/actions/xmleditor2/conf/kupu_config.xml');
        $content = preg_replace('/{\$xmlFile}/', htmlentities($xmlFile), $content);
        $content = preg_replace('/{\$expert_mode_allowed}/', $expert_mode_allowed, $content);
        $content = preg_replace('/{\$publication_allowed}/', $publication_allowed, $content);
        $content = preg_replace('/{\$checkspelling}/', $checkSpelling, $content);
        return $content;
    }

    public function validateSchema($idnode, $xmldoc)
    {
        $schema = $this->getSchemaFile($idnode);
        $xmldoc = "<?xml version='1.0' encoding='UTF-8'?>" . trim($xmldoc);
        $rngvalidator = new \Ximdex\XML\Validators\RNG();
        $valid = $rngvalidator->validate(\Ximdex\XML\Base::recodeSrc($schema, \Ximdex\XML\XML::UTF8), $xmldoc);
        $response = array('valid' => $valid, 'errors' => $rngvalidator->getErrors());
        return $response;
    }

    protected function enrichSchema($schema)
    {
        return XmlEditor_Enricher::enrichSchema($schema);
    }

    public function verifyTmpFile($idNode)
    {
        $response = array('method' => 'verifyTmpFile');
        if (!$idUser = \Ximdex\Runtime\Session::get('userID')) {
            $response['result'] = false;
            return $response;
        }
        $tmpFilePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . "/xedit_" . $idUser . "_" . $idNode;
        if (!file_exists($tmpFilePath) || !$response['tmp_mod_date'] = filectime($tmpFilePath)) {
            $response['result'] = false;
            return $response;
        }
        $dataFactory = new DataFactory($idNode);
        $lastVersion = $dataFactory->getLastVersionId();
        if (is_null($response['doc_mod_date'] = $dataFactory->GetDate($lastVersion)) || 
                $response['doc_mod_date'] >= $response['tmp_mod_date']) {
            $response['result'] = false;
            return $response;
        }
        $response['result'] = true;
        return $response;
    }

    public function removeTmpFile($idNode)
    {
        $response = array('method' => 'removeTmpFile');
        if (!$idUser = \Ximdex\Runtime\Session::get('userID')) {
            $response['result'] = false;
            return $response;
        }
        $tmpFilePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . "/xedit_" . $idUser . "_" . $idNode;
        if (file_exists($tmpFilePath) && !FsUtils::delete($tmpFilePath)) {
            $response['result'] = false;
        } else {
            $response['result'] = true;
        }
        return $response;
    }

    public function recoverTmpFile($idNode)
    {
        $response = array('method' => 'recoverTmpFile');
        if (!$idUser = \Ximdex\Runtime\Session::get('userID')) {
            $response['result'] = false;
            return $response;
        }
        $tmpFilePath = XIMDEX_ROOT_PATH . App::getValue('TempRoot') . "/xedit_" . $idUser . "_" . $idNode;
        if (!$this->setNode($idNode)) {
            Logger::error(_("A non-existing node cannot be saved: ") . $idNode);
            $response['result'] = false;
        } else {
            if (!$content = FsUtils::file_get_contents($tmpFilePath)) {
                $response['result'] = false;
            } else {
                $this->node->SetContent($content, true);
                $this->node->RenderizeNode();
                $response['result'] = true;
            }
        }
        return $response;
    }

    public function saveXmlFile($idNode, $content, $autoSave = false)
    {
        $response = array();
        $response['saved'] = false;
        $response['headers'] = array();
        $response['content'] = '';
        if (!$this->setNode($idNode)) {
            $msg = _("Document cannot be saved.");
            Logger::error(_("A non-existing node cannot be saved: ") . $idNode);
            $response['saved'] = false;
            $response['headers'][] = 'HTTP/1.1 200 Ok';
            
            // NOTE: Mozilla format
            $response['content'] = '<?xml version="1.0" encoding="UTF-8"?>
				<parsererror xmlns="http://www.mozilla.org/newlayout/xml/parsererror.xml">
				  Error while saving
				  <sourcetext>' . $msg . '</sourcetext></parsererror>';
        } else {

            // NOTE: Delete docxap tags and UID attributes
            $xmlContent = $this->_normalizeXmlDocument($idNode, $content);
            $xmlContent = \Ximdex\Utils\Strings::stripslashes($xmlContent);

            // Saving XML
            if ($autoSave === false) {
                $this->node->SetContent(\Ximdex\Utils\Strings::stripslashes($xmlContent), true);
                $this->node->RenderizeNode();
            } else {
                $idUser = \Ximdex\Runtime\Session::get('userID');
                if (!$idUser || !FsUtils::file_put_contents(XIMDEX_ROOT_PATH . App::getValue('TempRoot') . "/xedit_" . $idUser 
                        . "_" . $idNode, \Ximdex\Utils\Strings::stripslashes($xmlContent))) {
                    Logger::error(_("The content of " . $idNode . " could not be saved"));
                    return false;
                }
            }
            $response['saved'] = true;
            $response['headers'][] = 'HTTP/1.1 204 No Content';
        }
        $response['headers'][] = 'Content-type: text/xml';
        return $response;
    }

    public function publicateFile($idNode, $content)
    {
        $response = array();
        $response['publicated'] = false;
        $response['headers'] = array();
        $response['content'] = '';
        if (!$saveResponse = $this->saveXmlFile($idNode, $content)) {
            return $response;
        }
        $syncFacade = new SynchroFacade();
        if ($syncFacade->pushDocInPublishingPool($idNode, time())) {
            $response['publicated'] = true;
            $response['content'] = $saveResponse['content'];
        }
        $response['headers'][] = 'Content-type: text/xml';
        return $response;
    }

    public function getXmlFile($idNode, $view = null, $content = null)
    {
        if (!$this->setNode($idNode)) {
            Logger::error('A non-existing node content cannot be obtained: ' . $idNode);
            return false;
        }

        // TODO: Do correct docxap parametrize & insertion in document
        $nodeTypeName = $this->node->nodeType->get('Name');
        if ($nodeTypeName == 'RngVisualTemplate') {
            $content = sprintf('%s%s<docxap uid="%s.0" xmlns:xim="%s">%s</docxap>', App::getValue('EncodingTag')
                , App::getValue('DoctypeTag'), $idNode, ParsingRng::XMLNS_XIM, str_replace('<?xml version="1.0" encoding="UTF-8"?>'
                , '', $this->node->getContent()));
            return $content;
        }
        $dataFactory = new DataFactory($this->node->get('IdNode'));
        $lastVersion = $dataFactory->getLastVersionId();
        $args = [];
        $args['CHANNEL'] = $this->getDefaultChannel();
        $args['XEDIT_VIEW'] = $view;
        $args['DISABLE_CACHE'] = true;
        if (!is_null($content)) {
            $content = $this->_normalizeXmlDocument($idNode, $content);
            $content = \Ximdex\Utils\Strings::stripslashes($content);
        } else {
            $content = '';
        }
        $args['CONTENT'] = $content;
        $args['CALLER'] = 'xEDIT';
        $transition = new Transition();
        try {
            $res = $transition->process('FromXeditToPreFilter', $args, $lastVersion);
        } catch (Exception $e) {
            Logger::error($e->getMessage());
            return false;
        }
        return $res;
    }

    public function getSpellCheckingFile($idNode, $content)
    {
        $node = new Node($idNode);
        if (!$langId = $node->class->getLanguage()) {
            return false;
        }
        $lang = new Language($langId);
        $langISOName = $lang->GetIsoName();

        //TODO: Optimize words parsing from string
        $words = preg_split('/[^\wáéíóúàèìòùâêîôûäëïöüãõñçßæœÁÉÍÓÚÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÃÕÑÇẞÆŒ]+?/', $content, 0);
        $spellCheck = array();

        // New abstraction with php5-enchant module.
        if (!function_exists('enchant_broker_init')) {
            Logger::error(_('The php-enchant module should be installed to use the spell checker'));
        } else {
            $chkr = enchant_broker_init();
            if (!enchant_broker_dict_exists($chkr, $langISOName)) { //english as a default dictionary
                $dict = enchant_broker_request_dict($chkr, 'en_US');
            } else {
                $dict = enchant_broker_request_dict($chkr, $langISOName);
            }
            foreach ($words as $key => $word) {
                if ($word != null || strcmp($word, '') != 0) {
                    if (!enchant_dict_check($dict, $word)) {
                        $spellCheck[$key] = enchant_dict_suggest($dict, $word);
                    }
                }
            }
            enchant_broker_free_dict($dict); //Free a dictionary resource.
            enchant_broker_free($chkr); //Free checker resource
        }
        $spellCheckingDom = new DOMDocument('1.0', 'UTF-8');
        $spellCheckingDom->formatOutput = true;
        $spellCheckingDom->preserveWhiteSpace = false;
        $domRoot = $spellCheckingDom->createElement('spell_check');
        foreach ($spellCheck as $key => $value) {
            $element = $spellCheckingDom->createElement('word');
            $element->setAttribute('key', $key);
            $name = $spellCheckingDom->createElement('name');
            $wordText = $spellCheckingDom->createTextNode($words[$key]);
            $name->appendChild($wordText);
            $element->appendChild($name);
            if ($value != null) {
                foreach ($value as $suggestionValue) {
                    $suggestion = $spellCheckingDom->createElement('suggestion');
                    $suggestionText = $spellCheckingDom->createTextNode($suggestionValue);
                    $suggestion->appendChild($suggestionText);
                    $element->appendChild($suggestion);
                }
            }
            $domRoot->appendChild($element);
        }
        $spellCheckingDom->appendChild($domRoot);
        $xml = $spellCheckingDom->saveXML();
        return $xml;
    }

    public function getAnnotationFile($idNode, $content)
    {
        if (\Ximdex\Modules\Manager::isEnabled('Xowl')) {
            if (App::getValue('EnricherKey') === NULL || App::getValue('EnricherKey') == '') {
                Logger::error(_("Xowl_token configuration value has not been defined"));
                $resp = array(
                    "status" => "No  Xowl_token defined", 
                    "videourl" => "<center><iframe width='420' height='315' src='http://www.youtube.com/embed/xnhUzYKqJPw' frameborder='0' allowfullscreen></iframe></center>");
            } else {
                $ontologyService = new \Ximdex\Rest\Services\Xowl\OntologyService();
                $resp = $ontologyService->suggest($content);
            }
        } else {
            $videolink = "<a href='http://www.youtube.com/watch?v=xnhUzYKqJPw' target='_blank'>link</a>";
            $ximRAmsg = _("Xowl module has not been installed.<br/><br/> If you want to realize the noticeable improvements that you will obtain with Xowl, a demonstrative video is shown below (%s)<br/><br/>Also, you can test it at <a target='_blank' href='http://demo.ximdex.com'>demo.ximdex.com</a><br/><br/>");
            $videomsg = sprintf($ximRAmsg, $videolink);
            $urlvideo = "<center><iframe width='420' height='315' src='http://www.youtube.com/embed/xnhUzYKqJPw' frameborder='0' allowfullscreen></iframe></center>";
            Logger::error(_("Xowl module has not been installed. It is included in the advanced package WIX."));
            $resp = array("status" => $videomsg, "videourl" => $urlvideo);
        }
        return $resp;
    }

    public function getPreviewInServerFile($idNode, $content, $idChannel)
    {
        $node = new Node($idNode);
        $dataFactory = new DataFactory($idNode);
        $idVersion = $dataFactory->getLastVersionId();
        $args = [];
        $args['CHANNEL'] = $idChannel;
        $args['SERVERNODE'] = $node->getServer();
        $viewPreviewInServer = new ViewPreviewInServer();
        $content = $viewPreviewInServer->transform($idVersion, $content, $args);
        return $content;
    }

    public function getNoRenderizableElements($idNode)
    {
        if (!$this->setNode($idNode)) {
            return NULL;
        }
        
        // Obtaining idTemplate (RNG)
        $idcontainer = $this->node->getParent();
        $reltemplate = new \Ximdex\Models\RelTemplateContainer();
        $idTemplate = $reltemplate->getTemplate($idcontainer);

        // Obtaining RNG elements array
        $parser = new ParsingRng($idTemplate);
        $rngElements = $parser->getElements();

        // Obtaining array with templates referenced from templates_include.xsl
        $docxapId = NULL;
        $depsMngr = new DepsManager();
        if ($templatesIds = $depsMngr->getBySource(DepsManager::STRDOC_TEMPLATE, $idNode)) {
            foreach ($templatesIds as $templateId) {
                $templateNode = new Node($templateId);
                if ($templateNode->get('IdNode') > 0 && $templateNode->get('Name') == 'docxap.xsl')
                    $docxapId = $templateId;
            }
        }
        if (is_null($docxapId)) {
            Logger::error(_('docxap can not be found for node: ' . $this->node->GetNodeName()));
            return false;
        }
        $xslParser = new ParsingXsl($docxapId, null, $idNode);
        $templatesInclude = $xslParser->getIncludedElements('templates_include');
        if (!$templatesInclude)
        {
            // Invalid docxap content
            Logger::error('docxap has not valid content for node: ' . $this->node->GetNodeName());
            return false;
        }
        $templatesIncludePath = str_replace(App::getValue('UrlHost') . App::getValue('UrlRoot'), XIMDEX_ROOT_PATH, $templatesInclude[0]);
        $xslParser = new ParsingXsl(NULL, $templatesIncludePath);
        $templatesElements = $xslParser->getIncludedElements(NULL, true, true);
            
        // Obtaining no renderizable elements
        $intersectionElements = array_intersect($rngElements, $templatesElements);
        $norenderizableElements = array_diff($rngElements, $intersectionElements);
        $domDoc = new DOMDocument('1.0', 'UTF-8');
        $domDoc->formatOutput = true;
        $domDoc->preserveWhiteSpace = false;
        $domRoot = $domDoc->createElement('elements');
        $i = 0;
        foreach ($norenderizableElements as $noRenderizableElement) {
            $i++;
            $element = $domDoc->createElement('element');
            $element->setAttribute('index', $i);
            $elementTextNode = $domDoc->createTextNode($noRenderizableElement);
            $element->appendChild($elementTextNode);
            $domRoot->appendChild($element);
        }
        $domDoc->appendChild($domRoot);
        $xml = $domDoc->saveXML();
        return $xml;
    }

    private function setNode($idNode)
    {
        $this->node = new Node($idNode);
        if (!($this->node->get('IdNode') > 0)) {
            return false;
        }
        return true;
    }

    private function getDefaultChannel()
    {
        $channels = $this->node->getChannels();
        $defaultChannel = null;
        foreach ($channels as $channelID) {
            $channel = new Channel($channelID);
            $channelName = $channel->getName();
            if ($defaultChannel == null) $defaultChannel = $channelID;
            if (strToUpper($channelName) == 'HTML' || strToUpper($channelName) == 'WEB') {
                $defaultChannel = $channelID;
                break;
            }
        }
        return $defaultChannel;
    }

    private function canPublicate($idNode)
    {
        $user = new User(\Ximdex\Runtime\Session::get('userID'));
        if (\Ximdex\Modules\Manager::isEnabled('wix')) {
            return $user->HasPermissionInNode('Ximedit_publication_allowed', $idNode);
        } else {
            return false;
        }
    }

    private function getAllNamespaces()
    {
        $result = array();
        
        // Load from Xowl Service
        $namespacesArray = \Ximdex\Rest\Services\Xowl\OntologyService::getAllNamespaces();
        
        // For every namespace build an array. This will be a json object
        foreach ($namespacesArray as $namespace) {
            $array = array(
                "id" => $namespace->get("idNamespace"),
                "type" => $namespace->get("type"),
                "isSemantic" => $namespace->get("isSemantic"),
                "nemo" => $namespace->get("nemo"),
                "category" => $namespace->get("category"),
                "uri" => $namespace->get("uri")
            );
            $result[] = $array;
        }
        return $result;
    }
}
