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



final class Languages {

	const AUTO_DETECT = "";
	const ARABIC = "ar";
	const BULGARIAN = "bg";
	const CATALAN = "ca";
	const CHINESE = "zh";
	const CHINESE_SIMPLIFIED = "zh-CN";
	const CHINESE_TRADITIONAL = "zh-TW";
	const CROATIAN = "hr";
	const CZECH = "cs";
	const DANISH = "da";
	const DUTCH = "nl";
	const ENGLISH = "en";
	const FILIPINO = "tl";
	const FINNISH = "fi";
	const FRENCH = "fr";
	const GALACIAN = "gl";
	const GERMAN = "de";
	const GREEK = "el";
	const HEBREW = "iw";
	const HINDI = "hi";
	const HUNGARIAN = "hu";
	const INDONESIAN = "id";
	const ITALIAN = "it";
	const JAPANESE = "ja";
	const KOREAN = "ko";
	const LATVIAN = "lv";
	const LITHUANIAN = "lt";
	const MALTESE = "mt";
	const NORWEGIAN = "no";
	const POLISH = "pl";
	const PORTUGESE = "pt";
	const ROMANIAN = "ro";
	const RUSSIAN = "ru";
	const SERBIAN = "sr";
	const SLOVAK = "sk";
	const SLOVENIAN = "sl";
	const SPANISH = "es";
	const SWEDISH = "sv";
	const THAI = "th";
	const TURKISH = "tr";
	const UKRANIAN = "uk";
	const VIETNAMESE = "vi";
	
	public static function isValidLanguage($language) {

		// Reflection way (cheaper)
		$self_reflected_class = new ReflectionClass(__CLASS__);
		$self_defined_constants = $self_reflected_class->getConstants();

		return in_array($language, $self_defined_constants);
	}
}

?>