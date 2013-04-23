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
 *  @version $Revision: 8529 $
 */



ModulesManager::file('/actions/xmleditor2/model/XmlEditor_Abstract.class.php');
ModulesManager::file('/actions/xmleditor2/HTML2XML.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_PreviewInServer.class.php');
ModulesManager::file('/inc/nodetypes/xsltnode.inc');
ModulesManager::file('/inc/http/Curl.class.php');
//ModulesManager::file('/inc/rest/REST_Provider.class.php');
ModulesManager::file('/actions/enricher/model/Enricher.class.php', 'ximRA');
ModulesManager::file('/actions/enricher/model/TagSuggester.class.php', 'ximRA');
ModulesManager::file('/inc/repository/nodeviews/View_NodeToRenderizedContent.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_PrefilterMacros.class.php');
ModulesManager::file('/inc/repository/nodeviews/View_Xedit.class.php');
ModulesManager::file('/inc/parsers/ParsingXsl.class.php');
ModulesManager::file('/inc/sync/SynchroFacade.class.php');
ModulesManager::file('/inc/model/Versions.inc');
ModulesManager::file('/actions/xmleditor2/model/XmlEditor_Enricher.class.php');
ModulesManager::file('/inc/parsers/ParsingJsGetText.class.php');


class XmlEditor_KUPU extends XmlEditor_Abstract {

	private $domDoc = null;
	private $node = null;
	private $view = null;

	public function getEditorName() {

		return $this->_editorName;
	}

	public function getBaseURL() {

		return $this->_base_url;
	}

	public function setBaseURL($base_url) {

		$this->_base_url = $base_url;
	}

	public function setEditorName($editorName) {

		$this->_editorName = $editorName;
	}

	public function openEditor($idnode, $view) {

	    	$node = new Node($idnode);
    		if (!($node->get('IdNode') > 0)) {
			XMD_Log::error(_("A non-existing node cannot be obtained: ") . $node->get('IdNode'));
			return null;
		}

		$channels = $node->getChannels();
		$channelNames = array();
		foreach ($channels as $idChannel) {
			$channel = new Channel($idChannel);
			$channelName = $channel->getName();
			$channelNames[$idChannel] = $channelName;
		}

		$availableViews = array('tree');

		if (!$this->getXslFile($idnode, $view) || count($channels) == 0) {
			$view = 'tree';
		} else {
			$availableViews[] = 'normal';
		}

		if ((boolean)Config::getValue('PreviewInServer') && (boolean)count($channels)) {
			$availableViews[] = 'pro';
		}

		$nodeTypeName = $node->nodeType->GetName();
		$rngEditorMode = ($nodeTypeName == 'RngVisualTemplate') ? true : false;
		$dotdotPath = null;
		if($rngEditorMode === false) {
			$depth = $node->GetPublishedDepth();
			$dotdot = str_repeat('../', $depth - 2);
			$section = new Node($node->GetSection());
			$sectionPath = $section->class->GetNodeURL() . "/";
			$dotdotPath = $sectionPath . $dotdot;
		}

		$xmlFile = $this->_base_url . '&method=getXmlFile&view=' . $view;
	        $actionUrlShowPost = $this->_base_url . '&method=showPost';

        	$actionURL =  '/actions/xmleditor2';
	        $kupuURL = '/extensions/kupu';

        	$jsFiles = array(
//			'http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js',
			//'/actions/xmleditor2/js/extensions/jquery/js/jquery-1.3.2.min.js',
			Extensions::JQUERY,
//			'/actions/xmleditor2/js/extensions/jquery/js/jquery-1.3.2.js',
//			'/actions/xmleditor2/js/extensions/jquery/js/jquery-ui-1.7.custom.min.js',
//			'/actions/xmleditor2/js/extensions/jquery/js/jquery-ui-1.7.js',
			Extensions::JQUERY_UI,
			Extensions::JQUERY_PATH.'/js/fix.jquery.getters.js',
			Extensions::JQUERY_PATH.'/js/fix.jquery.parsejson.js',
			Extensions::JQUERY_PATH.'/plugins/jquery.json/jquery.json-2.2.min.js',
			'/inc/js/helpers.js',
			'/inc/js/sess.js',
			'/inc/js/collection.js',
			'/inc/js/dialogs.js',
			'/inc/js/ximtimer.js',
			'/inc/js/console.js',
			$kupuURL . '/common/sarissa.js',
        		$kupuURL . '/common/sarissa_ieemu_xpath.js',
	       	 	$kupuURL . '/common/kupuhelpers.js',
       			$kupuURL . '/common/kupubasetools.js',
        		$kupuURL . '/common/kupuloggers.js',
//		        $kupuURL . '/common/kupunoi18n.js',
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

				/*###Especial ToolBox###*/	
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

				/* ####### TOOLBOXES ########## */

		      	$actionURL . '/js/toolboxes/ToolbarButtonsToolBox.js',
			$actionURL . '/js/toolboxes/HighlightToolBox.class.js',
			$actionURL . '/js/toolboxes/DraggablesToolBox.class.js',
			$actionURL . '/js/toolboxes/ToolContainerToolBox.class.js',
			$actionURL . '/js/toolboxes/FloatingToolBox.class.js',
			$actionURL . '/js/toolboxes/XimdexLogger.class.js',
			$actionURL . '/js/toolboxes/InfoToolBox.class.js',
			$actionURL . '/js/toolboxes/AttributesToolBox.class.js',
			$actionURL . '/js/toolboxes/ChannelsToolBox.class.js',
			$actionURL . '/js/toolboxes/ChangesetToolBox.class.js',
			$actionURL . '/js/toolboxes/AnnotationToolBox.class.js',
			$actionURL . '/js/toolboxes/AnnotationRdfaToolBox.class.js',
			$actionURL . '/js/toolboxes/RNGElementsToolBox.class.js',
                        $actionURL . '/js/toolboxes/NavBarToolBox.class.js',
//			$actionURL . '/js/editor/ToolbarToolBox.class.js',

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

		$actionURL = Config::getValue('UrlRoot') . $actionURL;
	        $kupuURL = Config::getValue('UrlRoot') .$kupuURL;

	        $cssFiles = array(
			Config::getValue('UrlRoot') . '/xmd/style/jquery/custom_theme/jquery-ui-1.7.custom.css',
			$actionURL . '/views/common/css/kupustyles.css',
			$actionURL . '/views/common/css/toolboxes.css',
			$actionURL . '/views/common/css/treeview.css',
			Config::getValue('UrlRoot') . '/xmd/style/jquery/ximdex_theme/widgets/tabs/common_views.css',
			Config::getValue('UrlRoot') . '/xmd/style/jquery/ximdex_theme/widgets/treeview/treeview.css',
        	$kupuURL . '/common/kupudrawerstyles.css'
        	);

	        $baseTags = array(
			$kupuURL . '/common/'
	        );


		$_channels = array();
		foreach ($channelNames as $channelId=>$channel) {
			$_channels[] = sprintf("{channelId: %s, channel: '%s'}", $channelId, $channel);
		}
		$options = array(
			sprintf("editor_view: '%s'", $view),
			sprintf("rngEditorMode: '%s'", $rngEditorMode),
			sprintf("dotdotPath: '%s'", $dotdotPath),
			sprintf("availableViews: ['%s']", implode("','", $availableViews)),
			sprintf("channels: [%s]", implode(',', $_channels)),
		);

		$onloadfunctions = sprintf("kupu = startKupu({%s});", implode(", ", $options));

	        $values = array('nodeid' => $idnode,
        			'xmlFile' => $xmlFile,
        			'actionUrlShowPost' => $actionUrlShowPost,
       				'rngEditorMode' => $rngEditorMode,
       				'dotdotPath' => $dotdotPath,
       				'js_files' => $jsFiles,
       				'css_files' => $cssFiles,
       				'base_tags' => $baseTags,
       				'channels' => $channelNames,
				'on_load_functions' => $onloadfunctions
				);

		return $values;
	}

	public function getConfig($idNode) {

    	    	if (!$this->setNode($idNode)) {
			XMD_Log::error(_("A non-existing node cannot be edited: ") . $idNode);
    		}

		$hasPermission = Auth::hasPermission(XSession::get('userID'), 'expert_mode_allowed');
		$expert_mode_allowed = $hasPermission ?  '1' : '0';

		$canPublicate = $this->canPublicate($idNode);
		$publication_allowed = $canPublicate ?  '1' : '0';

		$xmlFile = $this->_base_url . '&method=getXmlFile';
		$content = FsUtils::file_get_contents(XIMDEX_ROOT_PATH . '/actions/xmleditor2/conf/kupu_config.xml');
		$content = preg_replace('/{\$xmlFile}/', htmlentities($xmlFile), $content);
		$content = preg_replace('/{\$expert_mode_allowed}/', $expert_mode_allowed, $content);
		$content = preg_replace('/{\$publication_allowed}/', $publication_allowed, $content);
		return $content;
	}

	private function transformHTML2XML($idnode, $htmldoc) {

		// Getting HTML with the changes & initial XML content
		$node = new Node($idnode);
		$xmlOrigenContent = $node->class->GetRenderizedContent();

		// Loading XML & HTML content into respective DOM Documents
		$docXmlOrigen = new DOMDocument();
		$docXmlOrigen->loadXML($xmlOrigenContent);
		$docHtml = new DOMDocument();
		$docHtml->loadHTML(String::stripslashes($htmldoc));

		// Transforming HTML into XML
		$htmlTransformer = new HTML2XML();
		$htmlTransformer->loadHTML($docHtml);
		$htmlTransformer->loadXML($docXmlOrigen);
		$htmlTransformer->setXimNode($idnode);

		$xmldoc = null;
		if ($htmlTransformer->transform()) {
			$xmldoc = $htmlTransformer->getXmlContent();
		}

		return $xmldoc;
	}

    public function validateSchema($idnode, $xmldoc) {
		$schema = $this->getSchemaFile($idnode);

		$xmldoc = '<?xml version="1.0" encoding="UTF-8"?>' . trim($xmldoc);

		$rngvalidator = new XMLValidator_RNG();
		$valid = $rngvalidator->validate(XmlBase::recodeSrc($schema, XML::UTF8), $xmldoc);
		$valid=true;
		$response = array('valid' => $valid,
						'errors' => $rngvalidator->getErrors()
						);

		return $response;
    }

    protected function enrichSchema($schema) {
    	return XmlEditor_Enricher::enrichSchema($schema);
    }

    public function verifyTmpFile($idNode) {
    	$response = array('method' => 'verifyTmpFile');
    	if(!$idUser = XSession::get('userID')) {
    		$response['result'] = false;
    		return $response;
    	}
    	$tmpFilePath = Config::getValue('AppRoot') . Config::getValue('TempRoot') . "/xedit_" . $idUser . "_"  . $idNode;

    	if(!file_exists($tmpFilePath) || !$response['tmp_mod_date'] = filectime($tmpFilePath)) {
    		$response['result'] = false;
    		return $response;
    	}

    	$dataFactory = new DataFactory($idNode);
    	$lastVersion = $dataFactory->GetLastVersionId();
    	if(	is_null($response['doc_mod_date'] = $dataFactory->GetDate($lastVersion)) ||
    		$response['doc_mod_date'] >= $response['tmp_mod_date']) {
    		$response['result'] = false;
    		return $response;
    	}

    	$response['result'] = true;
    	return $response;
    }

    public function removeTmpFile($idNode) {
    	$response = array('method' => 'removeTmpFile');
    	if(!$idUser = XSession::get('userID')) {
    		$response['result'] = false;
    		return $response;
    	}
    	$tmpFilePath = Config::getValue('AppRoot') . Config::getValue('TempRoot') . "/xedit_" . $idUser . "_"  . $idNode;

    	if(file_exists($tmpFilePath) && !FsUtils::delete($tmpFilePath)) {
    		$response['result'] = false;
    	} else {
    		$response['result'] = true;
    	}

    	return $response;
    }

    public function recoverTmpFile($idNode) {
    	$response = array('method' => 'recoverTmpFile');
    	if(!$idUser = XSession::get('userID')) {
    		$response['result'] = false;
    		return $response;
    	}
    	$tmpFilePath = Config::getValue('AppRoot') . Config::getValue('TempRoot') . "/xedit_" . $idUser . "_"  . $idNode;

    	if (!$this->setNode($idNode)) {
			XMD_Log::error(_("A non-existing node cannot be saved: ") . $idNode);
			$response['result'] = false;
    	} else {
			if(!$content = FsUtils::file_get_contents($tmpFilePath)) {
				$response['result'] = false;
			} else {
				$this->node->SetContent($content, true);
				$this->node->RenderizeNode();
				$response['result'] = true;
			}
    	}

    	return $response;
    }

	public function saveXmlFile($idNode, $content, $autoSave = false) {

		$response = array();
		$response['saved'] = false;
		$response['headers'] = array();
		$response['content'] = '';

    	if (!$this->setNode($idNode)) {
			$msg = _("Document cannot be saved.");

			XMD_Log::error(_("A non-existing node cannot be saved: ") . $idNode);

			$response['saved'] = false;
			$response['headers'][] = 'HTTP/1.1 200 Ok';
			// NOTE: Mozilla format
			$response['content'] = '<?xml version="1.0" encoding="UTF-8"?>
				<parsererror xmlns="http://www.mozilla.org/newlayout/xml/parsererror.xml">
				  Error while saving
				  <sourcetext>'.$msg.'</sourcetext></parsererror>';

		} else {

			// NOTE: Delete docxap tags and UID attributes
			$xmlContent = $this->_normalizeXmlDocument($idNode, $content);
			$xmlContent = String::stripslashes($xmlContent);

			// Saving XML
			if($autoSave === false) {
				$this->node->SetContent(String::stripslashes($xmlContent), true);
				$this->node->RenderizeNode();
			} else {
				$idUser = XSession::get('userID');
				if (!$idUser || !FsUtils::file_put_contents(Config::getValue('AppRoot') . Config::getValue('TempRoot') . "/xedit_" . $idUser . "_" . $idNode, String::stripslashes($xmlContent))) {
					XMD_Log::error(_("The content of " . $idNode ." could not be saved"));
					return false;
				}
			}

			$response['saved'] = true;
			$response['headers'][] = 'HTTP/1.1 204 No Content';

    	}

		$response['headers'][] = 'Content-type: text/xml';
		return $response;
	}

	public function publicateFile($idNode, $content) {
		$response = array();
		$response['publicated'] = false;
		$response['headers'] = array();
		$response['content'] = '';

		if(!$saveResponse = $this->saveXmlFile($idNode, $content))
			return $response;

		$syncFacade = new SynchroFacade();
		if($result = $syncFacade->pushDocInPublishingPool($idNode, mktime())) {
			$response['publicated'] = true;
			$response['content'] = $saveResponse['content'];
		}

		$response['headers'][] = 'Content-type: text/xml';
		return $response;
	}

	public function getXmlFile($idNode, $view, $content = null) {
		if(!$this->setNode($idNode)) {
			XMD_Log::error(_("A non-existing node content cannot be obtained: ") . $idNode);
			return false;
		}

		// TODO: Do correct docxap parametrize & insertion in document
		$nodeTypeName = $this->node->nodeType->get('Name');
		if ($nodeTypeName == 'RngVisualTemplate') {
			$content = sprintf('%s%s<docxap uid="%s.0" xmlns:xim="%s">%s</docxap>',Config::getValue('EncodingTag'), Config::getValue('DoctypeTag'),$idNode, PVD2RNG::XMLNS_XIM,str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $this->node->getContent()));
			return $content;
		}


		$dataFactory = new DataFactory($this->node->get('IdNode'));
		$lastVersion = $dataFactory->GetLastVersionId();
		$args['CHANNEL'] = $this->getDefaultChannel();
		$args['XEDIT_VIEW'] = $view;
		$args['DISABLE_CACHE'] = true;

		if(!is_null($content)) {
			$content = $this->_normalizeXmlDocument($idNode, $content);
			$content = String::stripslashes($content);
		} else {
			$content = '';
		}

		$args['CONTENT'] = $content;
		$args['CALLER'] = 'xEDIT';

		$pipelineManager = new PipelineManager();
		$content = $pipelineManager->getCacheFromProcessAsContent($lastVersion, 'StrDocToXedit', $args);
		return $content;
	}

	public function getSpellCheckingFile($idNode, $content) {

		$node = new Node($idNode);
		if(!$langId = $node->class->getLanguage())
			return false;
		$lang = new Language($langId);
		$langISOName = $lang->GetIsoName();
		//TODO: Optimize words parsing from string
		$content = ereg_replace("([A-Z])", ". \\1", $content);
		$words = explode("[ \.,\n\r\t\:]", $content);

		$spellCheck = array();

		//New abstraction with php5-enchant module.
		if(!function_exists('enchant_broker_init')){
			XMD_Log::error(_('The php5-enchant module should be installed to use the spell checker'));
		}
		else{
			$chkr = enchant_broker_init();
			if (!enchant_broker_dict_exists($chkr,$langISOName)) { //english as a default dictionary
				$dict = enchant_broker_request_dict($chkr, 'en_US');
			}
			else{
				$dict = enchant_broker_request_dict($chkr, $langISOName);
			}
			foreach($words as $key => $word) {
				if($word!=null || strcmp($word,'')!=0){
                                	$res = preg_split('/[\W]+?/', $word);
                                	if (isset($res[1]) && ($res[1] != '') && (strpos("'", $word) > 0) ) {
                                        	$res[0] = $word;
                                	}
                                	if(!enchant_dict_check($dict, $res[0])) {
                                        	$spellCheck[$key] = enchant_dict_suggest($dict,$res[0]);
                                	}
				}
                        }
			enchant_broker_free_dict($dict);//Free a dictionary resource.
			enchant_broker_free($chkr); //Free checker resource
		}

		$spellCheckingDom = new DOMDocument('1.0', 'UTF-8');
		$domRoot = $spellCheckingDom->createElement('spell_check');

		foreach($spellCheck as $key => $value) {
			$element = $spellCheckingDom->createElement('word');
			$element->setAttribute('key', $key);

			$name = $spellCheckingDom->createElement('name');
			$wordText = $spellCheckingDom->createTextNode($words[$key]);
			$name->appendChild($wordText);
			$element->appendChild($name);

			if($value != null){
				foreach($value as $suggestionValue) {
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

	public function getAnnotationFile($idNode, $content) {
		if(ModulesManager::isEnabled('ximRA')){
			if(Config::getValue('EnricherKey') === NULL || Config::getValue('EnricherKey') == '') {
				XMD_Log::error(_("EnricherKey configuration value has not been defined"));
				$resp = '{"status": "no  EnricherKey defined"}';
			} else {
				$enricher = new Enricher();
				$tagSuggester = new TagSuggester();
				$resp1 = XmlBase::recodeSrc($enricher->suggest($content, Config::getValue('EnricherKey'),'json'), XML::UTF8);
				$resp2 = XmlBase::recodeSrc($tagSuggester->suggest($content), XML::UTF8);

				//Build json response from zemanta and iks
				$status = "ok";
				$json1 = json_decode($resp1,true);
				$json2 = json_decode($resp2,true);

				if($json1['status']!="ok" && $json2['status']!="ok"){
					$status = "bad";
				}
				
				//We must delete de HTTP header that comes with the response
				$pos=strpos($resp1,"status");
				if ($pos!== FALSE){
		 	                $resp1=substr($resp1,$pos-2,strlen($resp1));
				}

				$resp='{"status": "'.$status.'","zemanta":'.$resp1.', "iks":'.$resp2.'}';
			}
		}
		else{
			$videolink="<a href='http://www.youtube.com/watch?v=xnhUzYKqJPw' target='_blank'>link</a>";	
			$ximRAmsg=_("ximRA module has not been installed.<br/><br/> If you want to realize the noticeable improvements that you will obtain with ximRA, a demonstrative video is shown below (%s)<br/><br/>Also, you can test it at <a target='_blank' href='http://demo.ximdex.com'>demo.ximdex.com</a><br/><br/>");
			$videomsg=sprintf($ximRAmsg,$videolink);
			$urlvideo = "<center><iframe width='420' height='315' src='http://www.youtube.com/embed/xnhUzYKqJPw' frameborder='0' allowfullscreen></iframe></center>";
			XMD_Log::error(_("ximRA module has not been installed. It is included in the advanced package WIX."));
		        $resp = '{"status": "'.$videomsg.'","videourl":"'.$urlvideo.'"}';
		}

		return $resp;
	}

	public function getPreviewInServerFile ($idNode, $content, $idChannel) {

		$node = new Node($idNode);

		$dataFactory = new DataFactory($idNode);
		$idVersion = $dataFactory->GetLastVersionId();

		$args['CHANNEL'] = $idChannel;
		$args['SERVERNODE'] = $node->getServer();

		$viewPreviewInServer = new View_PreviewInserver();
		$content = $viewPreviewInServer->transform($idVersion, $content, $args);

		return $content;
	}

	public function getNoRenderizableElements ($idNode) {

		if(!$this->setNode($idNode))
			return NULL;

		// Obtaining idTemplate (RNG)
		$idcontainer = $this->node->getParent();
		$reltemplate = new RelTemplateContainer();
		$idTemplate = $reltemplate->getTemplate($idcontainer);

		// Obtaining RNG elements array
		$parser = new ParsingRng($idTemplate);
		$rngElements = $parser->getElements();

		// Obtaining array with templates referenced from templates_include.xsl
		$templatesElements = array();
		$docxapId = NULL;
		$depsMngr = new DepsManager();
		if ($templatesIds = $depsMngr->getBySource(DepsManager::STRDOC_TEMPLATE, $idNode)) {
			foreach($templatesIds as $templateId) {
				$templateNode = new Node($templateId);
				if($templateNode->get('IdNode') > 0 && $templateNode->get('Name') == 'docxap.xsl')
					$docxapId = $templateId;
			}
		}

		if(is_null($docxapId)) {
			XMD_Log::error(_('docxap cannot be found.'));
		}

		$xslParser = new ParsingXsl($docxapId);
		$templatesInclude = $xslParser->getIncludedElements('templates_include');
		$templatesIncludePath = str_replace(Config::getValue('UrlRoot'), Config::getValue('AppRoot'),  $templatesInclude[0]);
		$xslParser = new ParsingXsl(NULL, $templatesIncludePath);
		$templatesElements = $xslParser->getIncludedElements(NULL, true, true);

		// Obtaining no renderizable elements
		$intersectionElements = array_intersect($rngElements, $templatesElements);
		$norenderizableElements = array_diff($rngElements, $intersectionElements);

		$domDoc = new DOMDocument('1.0', 'UTF-8');
		$domRoot = $domDoc->createElement('elements');

		$i = 0;
		foreach($norenderizableElements as $noRenderizableElement) {
			$i ++;
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

	private function setNode ($idNode) {
		$this->node = new Node($idNode);
		if(!($this->node->get('IdNode') > 0)) {
			return false;
		}
		return true;
	}

	private function getDefaultChannel() {
		$channels = $this->node->getChannels();

		$max = count($channels);
		$defaultChannel = null;
		for($i=0; $i<$max; $i++) {
			$channel = new Channel($channels[$i]);
			$channelName = $channel->getName();
			if ($defaultChannel == null) $defaultChannel =  $channels[$i];
			if (strToUpper($channelName) == 'HTML' || strToUpper($channelName) == 'WEB') {
				$defaultChannel = $channels[$i];
				break;
			}
		}

		return $defaultChannel;
	}

	private function canPublicate($idNode) {
		$user = new User(XSession::get('userID'));

		if(ModulesManager::isEnabled('wix')){
			return  $user->HasPermissionInNode('Ximedit_publication_allowed', $idNode);
		}else {
			return false;
		}
	}
}

?>
