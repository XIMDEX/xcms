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



require_once(XIMDEX_ROOT_PATH . '/inc/rest/providers/google_translate/Languages.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/rest/REST_Provider.class.php');
require_once(XIMDEX_ROOT_PATH . '/inc/serializer/Serializer.class.php');

class GoogleTranslate extends REST_Provider {

	const ENCODING = "UTF-8";
	const MAXCHARS = '5000';
	const URL_STRING = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0";

	public function __construct() {
		parent::__construct();
	}

	public function translate($text, $from, $to) {
		$textToTranslate = '';

		// gets only text nodes

		$domDoc = new DOMDocument();
		$domDoc->validateOnParse = true;
		$domDoc->preserveWhiteSpace = false;
		$domDoc->loadXML(\Ximdex\XML\Base::recodeSrc($text, \Ximdex\XML\XML::UTF8));

		$xpath = new DOMXPath($domDoc);

		$nodeList = $xpath->query('//text()');
		if ($nodeList->length > 0) {
			foreach ($nodeList as $element) {
				$textToTranslate .= $element->nodeValue . '|';
				$textNodes[] = array(
					'element' => $element,
					'tag' => $element->parentNode->nodeName,
					'text' => $element->nodeValue
				);
			}
		}

		// split text and request translation for chunks

		while (strlen($textToTranslate) > self::MAXCHARS) {
			$rest = substr($textToTranslate, 0, self::MAXCHARS);
			$end = strrpos($rest, '|');

			$chunks[] = substr($textToTranslate, 0, $end);
			$textToTranslate = substr($textToTranslate, $end);
		}

		$chunks[] = $textToTranslate;

		$translatedText = '';

		foreach ($chunks as $textChunk) {
			$translatedText .= $this->retrieveTranslation($textChunk, $from, $to);
		}

		$translatedNodes = explode('|', $translatedText);

		foreach ($textNodes as $n => $dataNode) {
    		$dataNode['element']->nodeValue = $translatedNodes[$n];
		}

		return $domDoc->saveXML();
	}

	private function retrieveTranslation($text, $from, $to) {

		if (!Languages::isValidLanguage($from) || !Languages::isValidLanguage($to) || $to == Languages::AUTO_DETECT) {
			// Exception
			return "dude, thats language is not valid\n";
		}

		// Check and encoding $text using ximdex class.

		$args = array (
				'langpair' => \Ximdex\XML\Base::recodeSrc($from . '|' . $to, \Ximdex\XML\XML::UTF8),
				'q' => \Ximdex\XML\Base::recodeSrc($text, \Ximdex\XML\XML::UTF8)
			);

		$data = "";
		foreach($args as $key=>$value) {
			$data .= ($data != "")?"&":"";
			$data .= urlencode($key)."=".urlencode($value);
		}

		$response = $this->http_provider->post(self::URL_STRING, $data);

		$result = Serializer::decode(SZR_JSON, $response['data']);

		if (is_null($result)) {
			XMD_Log::error("Lost in translation: error code {$response['http_code']}");
			return "{$response['data']}\n";
		}

		if ($result->responseStatus != 200) {
			XMD_Log::error("Lost in translation: error {$result->responseDetails}");
			return "Error: {$result->responseDetails}\n";
		}

		return urldecode($result->responseData->translatedText);
	}

}

?>