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








require_once(XIMDEX_ROOT_PATH . '/inc/widgets/WidgetDependencies.class.php');
require_once (XIMDEX_ROOT_PATH . '/inc/mvc/renderers/SmartyTextRenderer.class.php');

class Widget {



	// Smarty style
//	const START_TAG = '{\$';
//	const END_TAG = '}';
	// Ximdex style
	const START_TAG = '%=';
	const END_TAG = '%';

	// Widgets instances
	static protected $_instances = null;
	// Registered widgets
	static protected $_widgets = null;
	// Search regexps
	static protected $_widgetsRegexps = null;

	private function __construct() {
	}

	/**
	 * Finds all allowed widgets from file system and registers them
	 */
	static public function availableWidgets() {
		if (is_array(self::$_widgets)) return self::$_widgets;
		self::$_widgets = FsUtils::readFolder(dirname(__FILE__) , false, 'common');
		return self::$_widgets;
	}

	/**
	 * Returns valid regexps to looking for in templates
	 */
	static public function getWidgetsRegexps() {
		if (is_array(self::$_widgetsRegexps)) return self::$_widgetsRegexps;
		$widgets = self::availableWidgets();
		$regexps = array();
		foreach ($widgets as $widget) {
			$regexps[] = sprintf('|<(%s)([^>]*)></\\1>|i', $widget);
			$regexps[] = sprintf('|<(%s)([^>]*)[\s]*/>|i', $widget);
		}
		self::$_widgetsRegexps = $regexps;
		return $regexps;
	}

	static public function merge_arrays() {
		$args = func_get_args();
		$res = array();
		foreach ($args as $arr) {
			$res = array_unique(array_merge((array)$res, (array)$arr));
		}
		return $res;
	}

	static public function & getWidget($name) {

		if (!is_array(self::$_instances)) self::$_instances = array();

		$dir_widget = dirname(__FILE__);
		if(file_exists($dir_widget."/".$name) ) {
			$dir_widget = $dir_widget."/".$name;
		}

		$factory = new Factory($dir_widget, 'Widget_');
		$widget = $factory->instantiate($name);
		self::$_instances[$name] =& $widget;
		return $widget;
	}

	/**
	 * Process a widget instance and common resources
	 */
	static protected function _process($source, $options) {

		$options['js_files'] = isset($options['js_files']) ? $options['js_files'] : array();
		$options['css_files'] = isset($options['css_files']) ? $options['css_files'] : array();

		// Common resources
		$f = false;
		$widget =& self::getWidget('common');
		$cret = $widget->process(null);

		$js_files = self::merge_arrays($options['js_files'], $cret['js_files']);
		$css_files = self::merge_arrays($options['css_files'], $cret['css_files']);

		// $search is an array of regexps used for extract widgets tags
		$search = self::getWidgetsRegexps();

		foreach ($search as $pattern) {

			$ret = preg_match_all($pattern, $source, $matches, PREG_SET_ORDER);
			if ($ret) {

				// Iterate for each matched widget tag
				foreach ($matches as $match) {

					$tag = $match[0];
					$widget_name = $match[1];
					
					if("img" == $widget_name) continue;

					$widget =& self::getWidget($widget_name);

					if ($widget !== null && $widget->enable() ) {

						// $widget is now an instance of some particular widget class
						// $f flag is used for return null if no widget has been found on the template
						$f = true;

						// Substitute variables on the tag before extract its attributes
						foreach ($options as $key=>$value) {
							$pattern = sprintf('|%s%s%s|', self::START_TAG, $key, self::END_TAG);
							if (!is_array($value)) {
								$match[2] = preg_replace($pattern, $value, $match[2]);
							}
						}

						// Extract all attributes from the tag
						$params = array();
						$params["wtype"] = $widget_name;
						$ret = preg_match_all('|(?:(\w+)="([^"]*)")|', $match[2], $attributes, PREG_SET_ORDER);

						if ($ret) {
							foreach ($attributes as $attr) {
								$params[$attr[1]] = $attr[2];
							}
						}

						// process() method will return an array of dependencies (js, css, ...)
						// and the widget template
						$params["_enviroment"] = $options;

						$ret = $widget->process($params);

						if (is_array($ret)) {

							$css_files = self::merge_arrays($css_files, $ret['css_files']);
							$js_files = self::merge_arrays($js_files, $ret['js_files']);

							// Set attributes values on the template
							foreach ($ret['attributes'] as $key=>$value) {
								$pattern = sprintf('|%s[\s]*%s[\s]*%s|', self::START_TAG, $key, self::END_TAG);
								if (!is_array($value)) {
									$ret['tpl'] = preg_replace($pattern, $value, $ret['tpl']);
								}
							}

							// Set empty attributes (can't find a value in the options array)
							$pattern = sprintf('|%s[^%s]+%s|', self::START_TAG, self::END_TAG, self::END_TAG);
							$ret['tpl'] = preg_replace($pattern, '', $ret['tpl']);


							// Need to escape special chars on regexps...
							$tag = str_replace(
								array('$', '*', '?', '+', '^', '[', '('),
								array('\$', '\*', '\?', '\+', '\^', '\[', '\('),
								$tag
							);

							$a = self::_process($ret['tpl'], $options);

							if (is_array($a)) {
								$ret['tpl'] = $a['tpl'];
								$ret['tpl'] = SmartyTextRenderer::render($a['tpl'],  $a["attributes"] );
								$css_files = self::merge_arrays($css_files, $a['css_files']);
								$js_files = self::merge_arrays($js_files, $a['js_files']);
							}else {
								$ret['tpl'] = SmartyTextRenderer::render($ret['tpl'], $ret["attributes"]);
							}

							$source = preg_replace("|$tag|", $ret['tpl'], $source, 1);
						}
					}
				}
			}
		}

		if (!$f) return null;

//		$css_files = array_unique($css_files);
//		$js_files = array_unique($js_files);
//		$source = self::includeAssets($source, $js_files, $css_files);

		$ret = array(
			'tpl' => $source,
			'attributes' => $params,
			'js_files' => $js_files,
			'css_files' => $css_files
		);
		return $ret;
	}

	/**
	 * Process a widget instance and common resources
	 */
	static public function process($source, $options) {
		$ret = self::_process($source, $options);
		if ($ret === null) return null;

		$source = self::includeAssets($ret);

//debug::log($source);
		$ret['tpl'] = $source;

		return $ret;
	}

	static protected function includeAssets($data) {

		$source = $data['tpl'];
		$css_files = is_array($data['css_files']) ? $data['css_files'] : array();
		$css_files = array_unique($css_files);
		$js_files = is_array($data['js_files']) ? $data['js_files'] : array();
		$js_files = array_unique($js_files);

		$cssTag = sprintf('|%scss_widgets%s|', self::START_TAG, self::END_TAG);
		$jsTag = sprintf('|%sjs_widgets%s|', self::START_TAG, self::END_TAG);

		$include11 = preg_match($jsTag, $source);
		$include12 = preg_match($cssTag, $source);
		$include1 = $include11 || $include12;
		$include21 = preg_match('|class="js_to_include"|', $source);
		$include22 = preg_match('|class="css_to_include"|', $source);


//		debug::log($include12, $include1);
		$include2 = $include21 || $include22;
//		debug::log($include22, $include2);

		// 1. If we found widgets tags for include assets... do it, but skip li_for_js
		if ($include1) {
			$assets = array();
			foreach ($css_files as $css) {
				$assets[] = sprintf('<link type="text/css" href="%s" rel="stylesheet" />', $css);
			}
			$value = implode("\n", $assets);
			$source = preg_replace($cssTag, $value, $source);

			$assets = array();
			foreach ($js_files as $js) {
				$assets[] = sprintf('<script type="text/javascript" src="%s"></script>', $js);
			}
			$value = implode("\n", $assets);
			$source = preg_replace($jsTag, $value, $source);

			return $source;
		}

		$assets = array();
		foreach ($css_files as $css) {
			$assets[] = sprintf('<li>%s</li>', $css);
		}
		$css_assets = implode("\n", $assets);

		$assets = array();
		foreach ($js_files as $js) {
			$assets[] = sprintf('<li>%s</li>', $js);
		}
		$js_assets = implode("\n", $assets);

		// 2. If we could not find widgets tags, try to find li_for_js...
		if ($include2) {

			// 2.a. We found li_for_js, so prepend our assets to that list.
			$source = preg_replace('|<ul class="css_to_include">|', '<ul class="css_to_include">' . $css_assets, $source);
			$source = preg_replace('|<ul class="js_to_include">|', '<ul class="js_to_include">' . $js_assets, $source);

			return $source;
		}

		// 2.b. We could not find li_for_js, create our own list...
		$css_assets = '<ul class="css_to_include">' . $css_assets . '</ul>';
		$js_assets = '<ul class="js_to_include">' . $js_assets . '</ul>';
		$source .= sprintf('<div style="display: none;" class="widget_includes">%s%s</div>', $css_assets, $js_assets);

//		debug::log($source);
		return $source;
	}

	/**
	 * Returns a widget config file
	 * @param string wn Widget name
	 * @param string wi Widget ID
	 * @param string a Action name
	 * @param string m Module name
	 */
	static public function getWidgetConf($wn, $wi, $a, $m) {

		$defaultConf = sprintf('%s/%s/js/%s.conf.js', dirname(__FILE__),  $wn, $wn);

		$fileName = sprintf('%s_%s.conf.js', $wn, $wi);
		if (empty($wi)) {
			$fileName = sprintf('%s.conf.js', $wn);
		}

		$filePath = sprintf('%s/conf/', Config::getValue('AppRoot'));

		if (!empty($a)) {
			$filePath = sprintf('%s/actions/%s/conf/', Config::getValue('AppRoot'), $a);
		}

		if (!empty($m) && !empty($a)) {
			$filePath = sprintf('%s%s/actions/%s/conf/', Config::getValue('AppRoot'), ModulesManager::path($m) , $a);
		}

		/*if (!empty($m) && empty($a)) {
			$filePath = sprintf('%s/modules/%s/conf/', Config::getValue('AppRoot'), $m);
		}*/

		$filePath = sprintf('%s%s', $filePath, $fileName);
		if (!file_exists($filePath)) {
			$filePath = $defaultConf;
		}

		$content = FsUtils::file_get_contents($filePath);
//		debug::log($filePath, $content);
		return $content;
	}

	public function create($_wid, $vars = array() ) {
			$params="";
			if(array_key_exists("params", $vars) && !empty($vars["params"]) ) {
				foreach($vars["params"] as $key => $value) {
						$params .= " $key=\"$value\" ";

				}
			}

			$source = "<{$_wid} $params />";

			$content = self::process($source, $vars);

			return $content["tpl"];
	}


	/**
		update state of widgets
		 @param array params
	*/
	public function update($params) {
	}

}

?>
