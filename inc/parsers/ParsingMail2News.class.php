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



if (!defined ('XIMDEX_ROOT_PATH')) {
	define ('XIMDEX_ROOT_PATH', realpath (dirname (__FILE__)."/../../"));
}


class ParsingMail2News {

	protected $body;
	protected $parser;
	protected $subject;

	function __construct() {
		
		$this->parser = 'parser1';
		$this->body = '';
		$this->subject = '';
	}

	public function setBody($content = '') {

		$this->body = $content;
	}

	public function setSubject($content = '') {

		$this->subject = $content;
	}

	public function setParser($val) {

		$this->parser = $val;
	}

	/**
	 * @return string
	 */

	private function getBody() {

		return $this->body;
	}

	/**
	 * @return string
	 */
	
	private function getSubject() {

		return $this->subject;
	}

	/**
	 * @return string
	 */
	
	private function getParser() {

		return $this->parser;
	}

	/**
	*  Return the data of the new
	*  @return array / NULL
	*/

	public function getData() {

		return call_user_func_array ('self::' . $this->getParser(), array());
	}

	/**
	* Parsing option 1
	* Assumes that the news title is the mail subject and the news content is the mail body
	*  @return array / NULL
	*/

	private function parser1() {

		$newsData['title'] = $this->getSubject();
		$newsData['abstract'] = substr($this->getBody(), 0, 100);
		$newsData['paragraph'] = $this->getBody();

		return $newsData;
	}

	/**
	* Parsing option 2
	* Assumes that each line of given content contains a news element enclosed between brackets and after that its value
	*  @return array / NULL
	*/

	private function parser2() {

		$lines = explode("\n", $this->getBody());

		if (!is_null($lines)) {

			foreach ($lines as $line) {

				if (preg_match("/\[(.*)\](.*)/", $line, $regs) > 0) {
					$newsData[$regs[1]] = trim($regs[2]);
				}

			}

		}

		return isset($newsData) ? $newsData : NULL;
	}

	/**
	* Parsing option 3
	* Assumes thtat content is a valid XML schema and extract their elements, attributes and their values
	*  @return array / NULL
	*/

	private function parser3() {
		$doc = new DOMDocument;
		$doc->preserveWhiteSpace = false;
		
		$xml = Config::GetValue('EncodingTag') . $this->getBody();

		if (!$doc->LoadXML($xml)) {
			XMD_Log::error('Incorrect xml schema');
			exit();
		}

		$xpath = new DOMXPath($doc);

		// Parsing XML attributes

		$entriesAttributes = $xpath->query("//@*[string-length(.) > 0 and (local-name(.)!='type' and 
			local-name(.)!='name' and local-name(.)!='id' and local-name(.)!='boletin' and local-name(.)!='label')]");

		foreach ($entriesAttributes as $entry) {
			$newsData[$entry->nodeName] = $entry->nodeValue; 
		}

		// Parsing XML elements

		$entriesElements = $xpath->query("//*[text()]");

		foreach ($entriesElements as $entry) {

			if ($entry->hasAttribute('name')) {
				$name = $entry->attributes->getNamedItem('name')->nodeValue;
				$newsData[$name] = $entry->nodeValue; 
			}
		}

		return isset($newsData) ? $newsData : NULL;
	}

	/**
	* Parsing option 4
	* Assumes that first paragraph of given content is the news title, the second is the news abstract, 
	* and the third is the news paragraph
	*  @return array / NULL
	*/

	private function parser4() {
		$lines = explode("\n", $this->getBody());

		if (!is_null($lines)) {
			$newsData['title'] = $line[0];
			$newsData['entradilla'] = $line[1];
			$newsData['parrafo'] = $line[2];
		}

		return isset($newsData) ? $newsData : NULL;
	}
}
?>