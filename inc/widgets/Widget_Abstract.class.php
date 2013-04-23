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


require_once(XIMDEX_ROOT_PATH . '/inc/parsers/ParsingJsGetText.class.php');

abstract class Widget_Abstract {

	protected $_enable = true;
	protected $_wname = null;
	protected $_widget_dir = null;
	protected $_template_dir = null;
	protected $_js = null;
	protected $_css = null;
	protected $_tpl= null;

	/**
	 * Obtains widget dependencies/assets: js, css, tpl, ...
	 */
	public function __construct() {

		$this->_wname = str_replace('Widget_', '', get_class($this));
		$this->_widget_dir = sprintf('%s/%s', dirname(__FILE__),  $this->_wname);
		$this->_widget_style_dir = sprintf(
			'%s/xmd/style/jquery/%s/widgets/%s',
			XIMDEX_ROOT_PATH,
			XSession::get('activeTheme'),
			$this->_wname
		);

		$this->_assets_url = 'inc/widgets/';
		$this->_template_dir = sprintf('%s/template/', $this->_widget_dir);

		if($this->_enable) {
			$this->_js = WidgetDependencies::getDependencies($this->_wname);
			$this->_js = $this->fixJsUrl($this->_js);
			$this->setTemplate($this->_wname);
		}
	}

	public function enable() {
		return $this->_enable;
	}

	public function setEnable($_boolean) {
		$this->_enable = $_boolean;
	}

	protected function fixCssUrl($array, $wname) {
		$c = count($array);
		for ($i=0; $i<$c; $i++) {
			$array[$i] = sprintf(
				'%s/%s/%s',
				Config::getValue('UrlRoot'),
				preg_replace('#^'.realpath(Config::getValue('AppRoot')).'#', '', realpath($this->_widget_style_dir)),
				$array[$i]
			);
		}
		return $array;
	}

	protected function fixJsUrl($array) {
		$c = count($array);
		$getTextJs = new ParsingJsGetText();
		for ($i=0; $i<$c; $i++) {
			$array[$i] = $getTextJs->getFile(sprintf('/%s/%s', $this->_assets_url, $array[$i]) );
		}
		return $array;
	}

	public function getName() {
		return $this->_wname;
	}


	/** ____TPl____ **/
	public function setTemplate($_template) {
		if(null == $_template || !$this->enable() ) return null;

		$this->_tpl = sprintf('%s%s.tpl', $this->_template_dir, basename($_template));
		if (!is_file($this->_tpl)) {
			if ($this->_wname != 'common') XMD_Log::warning(sprintf("No existe plantilla para el widget %s", $this->_wname));
			$this->_tpl = null;
		}
	}


	public function getTemplate() {
		return $this->_tpl;
	}


	/** ___Css___ **/
	public function getCssFiles() {
		return $this->_css;
	}


	public function addCss($_css) {
		if(empty($this->_css) ) {
			$this->_css = array( 0 => $_css );
		}else {
			$this->_css[] = $_css;
		}
	}

	/** ___Js___ **/
	public function getJsFiles() {
		return $this->_js;
	}



	public function addJs($_js) {
		if(empty($this->_js) ) {
			$this->_js = array( 0 => $_js );
		}else if( !in_array($_js, $this->_js) ) {
			$this->_js[] = $_js;
		}
	}

	public function includeWidgetLib($name) {
		$widget =& Widget::getWidget($name);
		if ($widget) {
			$this->_js = array_unique(array_merge((array)$widget->getJsFiles(), (array)$this->_js));
			$this->_css = array_unique(array_merge((array)$widget->getCssFiles(), (array)$this->_css));
		}
	}

	protected function createJsParams($widgetId, $params) {
		$base = "xparams[wv][{$widgetId}]";
		$ret = array();
		foreach ($params as $key=>$value) {
			if(!is_array($value) )
				$ret[] = $base . "[{$key}]={$value}";
		}
		return $ret;
	}

	/**
	 * Parses the HTML tag attributes.
	 */
	protected function parseWidgetAttributes($params) {

		$ret = array();
		foreach ($params as $key=>$value) {
			switch ($key) {
				case 'paginatorShow':
					$ret[$key] = in_array(strtoupper($value), array('YES', 'TRUE'))
						? 'true' : 'false';
					$this->includeWidgetLib('itemsSelector');
					break;
				case 'useXVFS':
					$ret[$key] = in_array(strtoupper($value), array('YES', 'TRUE'))
						? 'true' : 'false';
					break;
				default:
					$ret[$key] = $value;
					break;
			}
		}

		if (!isset($ret['id'])) {
			$ret['id'] = sprintf('%s_%s', $this->_wname, rand());
		}

//		debug::log($ret);
		return $ret;
	}

	/**
	 * Default behaviour, overwrite if needed
	 */
	public function process($params) {

		//¿disabled?
		if( empty($this->_tpl) || !$this->enable() ) return null;
		/** ********************** ADD TPL DEFAULT ************* */
		$asInclude = isset($params['include']) && in_array(strtoupper($params['include']), array('YES', 'TRUE'))
			? true
			: false;

		unset($params['include']);


		$tpl = $asInclude
			? ''
			: FsUtils::file_get_contents($this->_tpl);

		/** ********************** ADD CSS DEFAULT ************* */
	   if(empty($this->_css)  && !is_array($this->_css)  ) {
	   	$this->_css = FsUtils::readFolder($this->_widget_style_dir . '/', false);
	   }
	  	if (!is_array($this->_css)) { $this->_css = array(); }

	  	$this->_css = $this->fixCssUrl($this->_css, $this->_wname);



		/** ********************** JS TRANSFORM ************* */


		// Make the attributes availables from Javascript codes
		$attributes = $this->parseWidgetAttributes($params);
		if (count($attributes) > 0 && !$asInclude) {
			$jsParams = $this->createJsParams($attributes['id'], $attributes);
			$url = sprintf('%s/xmd/loadaction.php?method=includeDinamicJs&%s&js_file=widgetsVars',
				Config::getValue('UrlRoot'), implode('&', $jsParams));
			$this->_js[] = $url;
//			debug::log($url);
		}

		/** ********************** PARAMS ************* */
		$ret = array(
			'tpl' => $tpl,
			'enable' => $this->_enable,
			'attributes' => $attributes,
			'js_files' => $this->_js,
			'css_files' => $this->_css
		);

		return $ret;
	}

}

?>
