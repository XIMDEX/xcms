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

ModulesManager::file('/inc/fsutils/FsUtils.class.php');

/**
 * Parser for a project under a specific path.
 * 
 * The path is defined in a xml file
 */
class BuildParser {

	/**
	 * Full path to config file
	 * @var string
	 */
	private $filePath = null;
	/**
	 * Xml object for the config file
	 * @var DomDocument
	 */
	private $doc = null;
	/**
	 * XPath object for the config file
	 * @var XPath
	 */
	private $xpath = null;


	/**
	*Constructor. Load project from schema and theme style. It could be the default one.
	*@param $themeStyle (optional). It could be Bootstrap, default or whatever
	*/
	public function __construct($name=null){

		$patternProjectPath = Config::GetValue("AppRoot").THEMES_FOLDER."/%s/";
		$isDefault = false;

		$buildFilePath = sprintf($patternProjectPath,$name).PROJECT_CONFIG_FILENAME;
		
		$this->filePath = $buildFilePath;

		/*If build file not exists in projects folder for this version and theme
		style, write message*/
		if (!file_exists($buildFilePath)){
			if ($isDefault){
				XMD_Log::fatal(LOG_PREFIX."Default Build file doesn't found in this path: $buildFilePath.");
				return false;
			}
			XMD_Log::error(LOG_PREFIX." Build file doesn't found in this path: $buildFilePath. It will load Default project");
		}else{
			$domDocument = new DomDocument();			
			$domDocument->load($buildFilePath);
			$this->doc = $domDocument;
			if (!$this->doc){
				error_log("Error al cargar $buildFilePath");
			}
			$this->xpath = new DOMXPath($this->doc);
			if (!$this->xpath){
				error_log("Error al cargar XPath para $buildFilePath");
			}
		}
	}

	/**
	*<p>Get a Loader_Project object from the build.xml file.</p>
	*@return Loader_Project object
	**/
	public function getProject() {
		$query = '/project';
		$items = $this->xpath->query($query);
		return new Loader_Project($items->item(0), $this->xpath, dirname($this->filePath));
	}
}

class Loader_XimFile {

	protected $path = null;
	protected $basename = null;
	protected $dirname = null;
	protected $filename = null;
	protected $extension = null;
	protected $nodetypename = null;

	public function __construct($nodeTypeName, $path) {

		$info = pathinfo($path);
		if (isset($info['extension'])) {
			$filename = preg_replace(sprintf('/\.%s$/', $info['extension']), '', $info['basename']);
		} else {
			$filename = $info['basename'];
		}

		$this->nodetypename = $nodeTypeName;
		$this->path = $path;
		$this->dirname = $info['dirname'];
		$this->basename = $info['basename'];
		$this->extension = $info['extension'];
		$this->filename = $filename;
	}

	public function getContent() {
		return FsUtils::file_get_contents($this->path);
	}

	public function setContent($content) {
		return FsUtils::file_put_contents($this->path, $content);
	}

	public function getPath() {
		return $this->path;
	}

	public function __get($name) {
		return property_exists($this, $name) ? $this->$name : null;
	}
}

abstract class Loader_AbstractNode {

	protected $basepath = null;
	protected $xpath = null;
	protected $node = null;
	protected $data = null;

	public function __construct($node, $xpath, $basepath) {
		$this->node = $node;
		$this->xpath = $xpath;
		$this->basepath = $basepath;
		$this->parseAttributes($this->node);
	}

	abstract protected function getValidAttributes();

	protected function parseAttributes($node) {
		$this->data = array();
		foreach ($node->attributes as $attr) {
			if (!$this->isValidAttribute($attr->name)) continue;
			$this->data[$attr->name] = $attr->value;
		}
		return $this->data;
	}

	protected function isValidAttribute($name) {
		$va = $this->getValidAttributes();
		return in_array($name, $va);
	}

	public function getPath() {
                return $this->basepath;
	}

	public function __get($name) {
		//var_dump($name);
		if (!$this->isValidAttribute($name)) return null;
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	public function __set($name, $value) {
		if (!$this->isValidAttribute($name)) return null;
		$this->data[$name] = $value;
	}
}

class Loader_Project extends Loader_AbstractNode {

	protected function getValidAttributes() {
		return array('projectid', 'name', 'nodetypename', 'Transformer', 'channel', 'language', 'title', 'description');
	}

	public function getSchemes() {

		//Backward compatibility. Allow xml transformer.
		$extension = 'xml';
		$nodetypename = 'RNGVISUALTEMPLATE';

		$path = $this->getPath() . '/schemes';
	    $files = FsUtils::readFolder($path, false);	    
		$ret = array();
		if ($files){
			foreach ($files as $file) {				
	            if (preg_match(sprintf('/\.%s$/', $extension), $file)) {
					$ret[] = new Loader_XimFile($nodetypename, "$path/$file");
	            }
			}	
		}		
		return $ret;
	}

	public function getServers() {
		$query = sprintf("//section[@nodetypename='SERVER']");
		$items = $this->xpath->query($query, $this->node);
		$ret = array();
		if ($items){
			foreach ($items as $item) {
				$ret[] = new Loader_Server($item, $this->xpath, $this->getPath());
			}	
		}		
		return $ret;
	}

	public function getXimlink() {
		$path = $this->getPath() . '/ximlink';
		$files = FsUtils::readFolder($path, false);
		$ret = array();
		foreach ($files as $file) {
			$ret[] = new Loader_XimFile('LINK', "$path/$file");
		}
		return $ret;
	}

	public function getTemplates($type='XSL') {

		//Backward compatibility. Allow xml transformer.
		$extension = $type == 'XSL' ? 'xsl': 'xml';
		$nodetypename = $type == 'XSL' ? 'XSLTEMPLATE': 'TEMPLATE';

		$path = $this->getPath() . '/templates';
	    $files = FsUtils::readFolder($path, false);	    
		$ret = array();
		if ($files){
			foreach ($files as $file) {
	            if (preg_match(sprintf('/\.%s$/', $extension), $file)) {
					$ret[] = new Loader_XimFile($nodetypename, "$path/$file");
	            }
			}
		}	
		return $ret;
	}
}

class Loader_Section extends Loader_AbstractNode {

	protected function getValidAttributes() {
		return array('name', 'nodetypename', 'DefaultSchema', 'Transformer', 'channel', 'language');
	}

	public function getSections() {
		$query = sprintf("//section[@nodetypename='SECTION']");
		$items = $this->xpath->query($query, $this->node);
		$ret = array();
		foreach ($items as $item) {
			$ret[] = new Loader_Server($item, $this->xpath, $this->getPath());
		}
		return $ret;
	}

	public function getXimdocs() {
		$query = sprintf('//ximdoccontainer/document');
		$items = $this->xpath->query($query, $this->node);
		$ret = array();
		foreach ($items as $item) {
			$ret[] = new Loader_XimDOC($item, $this->xpath, $this->getPath());
		}
		return $ret;
	}

	public function getXimlets() {
		$query = sprintf('//ximletcontainer/ximlet');
		$items = $this->xpath->query($query, $this->node);
		$ret = array();
		foreach ($items as $item) {
			$ret[] = new Loader_XimLET($item, $this->xpath, $this->getPath());
		}
		return $ret;
	}

	
	public function getCommon($recursiveFolderName="") {
		$path = $this->getPath() . '/common'.$recursiveFolderName;
		$files = FsUtils::readFolder($path, false);
		$recursiveRet = $ret = array();
		if ($files){
			foreach ($files as $file) {				
				$newRecursiveFolderName = "";
				if (is_dir("$path/$file")){
					$newRecursiveFolderName = "$recursiveFolderName/$file";
					$recursiveRet = array_merge($this->getCommon($newRecursiveFolderName),$recursiveRet);
				}else{
					$ret["$recursiveFolderName/$file"] = new Loader_XimFile('TextFile', "$path/$file");
				}
			}
		}		
		return array_merge($recursiveRet,$ret);
	}

	public function getImages($recursiveFolderName="") {
		$path = $this->getPath() . "/images$recursiveFolderName";
		$files = FsUtils::readFolder($path, false);
		$recursiveRet = $ret = array();
		if ($files){
			foreach ($files as $file) {
				$newRecursiveFolderName = "";
				if (is_dir("$path/$file")){
					$newRecursiveFolderName = "$recursiveFolderName/$file";
					$recursiveRet = array_merge($this->getImages($newRecursiveFolderName),$recursiveRet);
				}else{
	                $ret["$recursiveFolderName/$file"] = new Loader_XimFile('IMAGEFILE', "$path/$file");
	                
	            }
			}	
		}
		return array_merge($recursiveRet,$ret);
	}

	public function getXimclude() {
		$path = $this->getPath() . '/ximclude';
		$files = FsUtils::readFolder($path, false);
		$ret = array();
		foreach ($files as $file) {
			$ret[] = new Loader_XimFile('LINK', "$path/$file");
		}
		return $ret;
	}

	public function getXimlet() {
		$query = sprintf('//ximletcontainer/ximlet');
		$items = $this->xpath->query($query, $this->node);
		$ret = array();
		foreach ($items as $item) {
			$ret[] = new Loader_XimLET($item, $this->xpath, $this->getPath());
		}
		return $ret;
	}

	public function getTemplates($type="XSL") {

		//Backward compatibility. Allow xml transformer.
		$extension = $type == 'XSL' ? 'xsl': 'xml';
		$nodetypename = $type == 'XSL' ? 'XSLTEMPLATE': 'TEMPLATE';

		$path = $this->getPath() . '/templates';
	    $files = FsUtils::readFolder($path, false);	    
		$ret = array();
		if ($files){
			foreach ($files as $file) {
	            if (preg_match(sprintf('/\.%s$/', $extension), $file)) {
					$ret[] = new Loader_XimFile($nodetypename, "$path/$file");
	            }
			}
		}	
		return $ret;
	}
}

class Loader_Server extends Loader_Section {

	public function __construct($node, $xpath, $basepath) {
		parent::__construct($node, $xpath, $basepath);
		$this->data['isServerOTF'] = $this->data['isServerOTF'] == 'false'
			? false
			: true;
	}

        public function getPath(){
            return $this->basepath."/Server";
        }

	protected function getValidAttributes() {
		return array(
			'serverid', 'name', 'nodetypename', 'protocol',
			'login', 'password', 'host', 'port',
			'url', 'initialDirectory', 'overrideLocalPaths',
			'enabled', 'previsual', 'description', 'isServerOTF'
		);
	}

	public function getXimdocs() {
		$query = sprintf('//ximdoccontainer/document');
		$items = $this->xpath->query($query, $this->node);
		$ret = array();
		foreach ($items as $item) {
			$ret[] = new Loader_XimDOC($item, $this->xpath, $this->getPath());
		}
		return $ret;
	}

	public function getCSS($recursiveFolderName="") {
		$path = $this->getPath() . "/css$recursiveFolderName";
		$files = FsUtils::readFolder($path, false);
		$recursiveRet = $ret = array();
		if ($files){
			foreach ($files as $file) {
				$newRecursiveFolderName = "";
				if (is_dir("$path/$file")){
					$newRecursiveFolderName = "$recursiveFolderName/$file";
					$recursiveRet = array_merge($this->getCss($newRecursiveFolderName),$recursiveRet);
				}else{
					$ret["$recursiveFolderName/$file"] = new Loader_XimFile('CSSFILE', "$path/$file");
				}
			}			
		}
		
    	return array_merge($recursiveRet,$ret);
	}
}

class Loader_XimDOC extends Loader_AbstractNode {

	protected function getValidAttributes() {
		return array('name', 'nodetypename', 'description', 'templatename', 'channel', 'language');
	}

	public function getPath() {
		return $this->basepath . '/documents/' . $this->name . '.xml';
	}

	public function getContent() {
		return FsUtils::file_get_contents($this->getPath());
	}

	public function setContent($content) {
		return FsUtils::file_put_contents($this->getPath(), $content);
	}
}

class Loader_XimLET extends Loader_AbstractNode{

protected function getValidAttributes() {
		return array('name', 'nodetypename', 'templatename', 'channel', 'language');
	}

	public function getPath() {
		return $this->basepath . '/ximlet/' . $this->name . '.xml';
	}

	public function getContent() {
		return FsUtils::file_get_contents($this->getPath());
	}

	public function setContent($content) {
		return FsUtils::file_put_contents($this->getPath(), $content);
	}
}

class Loader_Scheme extends Loader_AbstractNode{
	protected function getValidAttributes(){
		return array("name", "nodetypename");
	}

	public function getPath(){
		return $this->basepath."/schemes/".$this->name.".xml";
	}

	public function getContent() {
		return FsUtils::file_get_contents($this->getPath());
	}

	public function setContent($content) {
		return FsUtils::file_put_contents($this->getPath(), $content);
	}
}

?>