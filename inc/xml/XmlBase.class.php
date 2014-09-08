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



if(!defined("XIMDEX_ROOT_PATH"))
	define("XIMDEX_ROOT_PATH", realpath(dirname(__FILE__)."/../../"));

include_once (XIMDEX_ROOT_PATH."/inc/xml/XML.class.php");
include_once (XIMDEX_ROOT_PATH."/inc/fsutils/FsUtils.class.php");
	
class XmlBase {

	public static $html_translation_table = array(
				" "=>"&nbsp;", "¡"=>"&iexcl;", "¢"=>"&cent;", "£"=>"&pound;",
				"¤"=>"&curren;", "¥"=>"&yen;", "¦"=>"&brvbar;", "§"=>"&sect;",
				"¨"=>"&uml;", "©"=>"&copy;", "ª"=>"&ordf;", "«"=>"&laquo;",
				"¬"=>"&not;", "­"=>"&shy;", "®"=>"&reg;", "¯"=>"&macr;",
				"°"=>"&deg;", "±"=>"&plusmn;", "²"=>"&sup2;", "³"=>"&sup3;",
				"´"=>"&acute;", "µ"=>"&micro;", "¶"=>"&para;", "·"=>"&middot;",
				"¸"=>"&cedil;", "¹"=>"&sup1;", "º"=>"&ordm;", "»"=>"&raquo;",
				"¼"=>"&frac14;", "½"=>"&frac12;", "¾"=>"&frac34;", "¿"=>"&iquest;",
				"À"=>"&Agrave;", "Á"=>"&Aacute;", "Â"=>"&Acirc;", "Ã"=>"&Atilde;",
				"Ä"=>"&Auml;", "Å"=>"&Aring;", "Æ"=>"&AElig;", "Ç"=>"&Ccedil;",
				"È"=>"&Egrave;", "É"=>"&Eacute;", "Ê"=>"&Ecirc;", "Ë"=>"&Euml;",
				"Ì"=>"&Igrave;", "Í"=>"&Iacute;", "Î"=>"&Icirc;", "Ï"=>"&Iuml;",
				"Ð"=>"&ETH;", "Ñ"=>"&Ntilde;", "Ò"=>"&Ograve;", "Ó"=>"&Oacute;",
				"Ô"=>"&Ocirc;", "Õ"=>"&Otilde;", "Ö"=>"&Ouml;", "×"=>"&times;",
				"Ø"=>"&Oslash;", "Ù"=>"&Ugrave;", "Ú"=>"&Uacute;", "Û"=>"&Ucirc;",
				"Ü"=>"&Uuml;", "Ý"=>"&Yacute;", "Þ"=>"&THORN;", "ß"=>"&szlig;",
				"à"=>"&agrave;", "á"=>"&aacute;", "â"=>"&acirc;", "ã"=>"&atilde;",
				"ä"=>"&auml;", "å"=>"&aring;", "æ"=>"&aelig;", "ç"=>"&ccedil;",
				"è"=>"&egrave;", "é"=>"&eacute;", "ê"=>"&ecirc;", "ë"=>"&euml;",
				"ì"=>"&igrave;", "í"=>"&iacute;", "î"=>"&icirc;", "ï"=>"&iuml;",
				"ð"=>"&eth;", "ñ"=>"&ntilde;", "ò"=>"&ograve;", "ó"=>"&oacute;",
				"ô"=>"&ocirc;", "õ"=>"&otilde;", "ö"=>"&ouml;", "÷"=>"&divide;",
				"ø"=>"&oslash;", "ù"=>"&ugrave;", "ú"=>"&uacute;", "û"=>"&ucirc;",
				"ü"=>"&uuml;", "ý"=>"&yacute;", "þ"=>"&thorn;", "ÿ"=>"&yuml;",
				"\""=>"&quot;", "'"=>"&#39;", "<"=>"&lt;", ">"=>"&gt;", "&"=>"&amp;"
		);
	
	public static $numericHtml_translation_table = array (
				" "=>"&#160;", "¡"=>"&#161;", "¢"=>"&#162;", "£"=>"&#163;",
				"¤"=>"&#164;", "¥"=>"&#165;", "¦"=>"&#166;", "§"=>"&#167;",
				"¨"=>"&#168;", "©"=>"&#169;", "ª"=>"&#170;", "«"=>"&#171;",
				"¬"=>"&#172;", "­"=>"&#173;", "®"=>"&#174;", "¯"=>"&#175;",
				"°"=>"&#176;", "±"=>"&#177;", "²"=>"&#178;", "³"=>"&#179;",
				"´"=>"&#180;", "µ"=>"&#181;", "¶"=>"&#182;", "·"=>"&#183;",
				"¸"=>"&#184;", "¹"=>"&#185;", "º"=>"&#186;", "»"=>"&#187;",
				"¼"=>"&#188;", "½"=>"&#189;", "¾"=>"&#190;", "¿"=>"&#191;",
				"À"=>"&#192;", "Á"=>"&#193;", "Â"=>"&#194;", "Ã"=>"&#195;",
				"Ä"=>"&#196;", "Å"=>"&#197;", "Æ"=>"&#198;", "Ç"=>"&#199;",
				"È"=>"&#200;", "É"=>"&#201;", "Ê"=>"&#202;", "Ë"=>"&#203;",
				"Ì"=>"&#204;", "Í"=>"&#205;", "Î"=>"&#206;", "Ï"=>"&#207;",
				"Ð"=>"&#208;", "Ñ"=>"&#209;", "Ò"=>"&#210;", "Ó"=>"&#211;",
				"Ô"=>"&#212;", "Õ"=>"&#213;", "Ö"=>"&#214;", "×"=>"&#215;",
				"Ø"=>"&#216;", "Ù"=>"&#217;", "Ú"=>"&#218;", "Û"=>"&#219;",
				"Ü"=>"&#220;", "Ý"=>"&#221;", "Þ"=>"&#222;", "ß"=>"&#223;",
				"à"=>"&#224;", "á"=>"&#225;", "â"=>"&#226;", "ã"=>"&#227;",
				"ä"=>"&#228;", "å"=>"&#229;", "æ"=>"&#230;", "ç"=>"&#231;",
				"è"=>"&#232;", "é"=>"&#233;", "ê"=>"&#234;", "ë"=>"&#235;",
				"ì"=>"&#236;", "í"=>"&#237;", "î"=>"&#238;", "ï"=>"&#239;",
				"ð"=>"&#240;", "ñ"=>"&#241;", "ò"=>"&#242;", "ó"=>"&#243;",
				"ô"=>"&#244;", "õ"=>"&#245;", "ö"=>"&#246;", "÷"=>"&#247;",
				"ø"=>"&#248;", "ù"=>"&#249;", "ú"=>"&#250;", "û"=>"&#251;",
				"ü"=>"&#252;", "ý"=>"&#253;", "þ"=>"&#254;", "ÿ"=>"&#255;",
				"\""=>"&#34;", "'"=>"&#39;", "<"=>"&#60;", ">"=>"&#62;",
				"&"=>"&#38;"
		);

		

	// Returns true if $string is valid UTF-8 and false otherwise.
	public static function isUtf8($string) {
		if (!is_object($string)) {
			$encode= mb_detect_encoding($string,"UTF-8, ISO-8859-1");
		}
		return ( $encode == "UTF-8");
	}

	/**
	 * Recode the src about encoding parama
	 *
	 * @param String $src
	 * @param String $encoding
	 * @return String, recode src
	 */
	public static function recodeSrc($src, $encoding) {

		$isUtf8 = XmlBase::isUtf8($src." ");
		if ($src == null){
			//utf8_encode encode the null value in ''
			$ret=$src;
		}else if($encoding == XML::UTF8) {
			if(!$isUtf8) {
				$ret = XmlBase::_unicodeToHtmlEntities($src);
				$ret = str_replace('&amp;', 'MAP_GEN_CODE_AMP', $ret);
				$ret = html_entity_decode($ret);
				$ret = str_replace('MAP_GEN_CODE_AMP', '&amp;', $ret);
				$ret = utf8_encode($ret);
			} else {
				//src is UTF-8, we dont do anything
				$ret=$src;
			}
		// default: $encoding == XML::ISO88591
		} else {
			if($isUtf8) {
				$ret = utf8_decode($src);
			} else {
				$ret=$src;
			}
		}
		
		return $ret;
	}
	
	/**
	 * Recode a file about the encoding
	 * 
	 * @param String $name
	 * @param String $encoding
	 */
	public static function recodeFile($name, $encoding){
		$initContent = FsUtils::file_get_contents($name);
		$contentRecode = XmlBase::recodeSrc($initContent, $encoding);
		return FsUtils::file_put_contents($name,$contentRecode);
	}
	protected static function _hasHtmlEntities($src) {
		$hasEntities = false;
		foreach(XmlBase::_html_translation_table as $utf8 => $entity) {
			$numEntity = XmlBase::$_numericHtml_translation_table[$utf8];
			$hasEntities = !(strpos($src, $entity) === false) || !(strpos($src, $numEntity) === false);
			if($hasEntities) {
				break;
			}
		}
		return $hasEntities;
	}

	protected static function _unicodeToHtmlEntities($input) {
		$htmlEntities = array_values(XmlBase::$html_translation_table);
		return str_replace(XmlBase::$numericHtml_translation_table, $htmlEntities, $input);
	}

	// Genera las entidades html
	public static function generateHtmlEntities() {
		$ret = '';
		$entities = XmlBase::_html_translation_table;
		foreach($entities as $key => $value) {
			if($key == '"') {
				$key = str_replace($key, "&#34;", $key);
			}
			$ret .= '<!ENTITY '.$value.' "'.$key.'">'."\n";
		}
		return $ret;
	}

	public static function htmlentitiesWithoutTags($src)
	{
		$ret='';
		$i=0;
		for ($i=0;$i<strlen($src);$i++){
			//position next start tag
			$posNextTagIni = strpos($src,'<', $i);
			//position next end tag '/>'
			$posNextTagFin = strpos($src,'</', $i);
			//do htmlentities until the position next start tag
			if ($posNextTagIni>$i)
				replace(substr($src,$i,$posNextTagIni-$i),htmlentities(substr($src,$i,$posNextTagIni-$i)), $src);

			//iterator before the next end tag
			$i=$posNextTagFin+1;
		}

		return $ret;

	}

	public static function encodeSimpleElement($value, $encoding)
	{
		//echo "<h4>encoding the element $value";
		return (XmlBase::recodeSrc($value,$encoding));
	}
	public static function encodeArrayElement($array, $encoding)
	{
		foreach ($array as $key=>$element)
		{
			//echo '<h3>Codificando el array</h3>';
			
			//echo "$key --> $element";
			if (is_array($element))
			{
				$array[$key]=XmlBase::encodeArrayElement($array[$key],$encoding);
			}
			else
			{
				$array[$key]=XmlBase::encodeSimpleElement($array[$key],$encoding);
			}
		}
		return $array;
	}

}
?>