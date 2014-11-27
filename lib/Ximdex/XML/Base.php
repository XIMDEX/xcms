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
 * @author Ximdex DevTeam <dev@ximdex.com>
 * @version $Revision$
 */

namespace Ximdex\XML;

use  FsUtils;

class Base extends XML
{

    public static $html_translation_table = array(
        "�" => "&nbsp;", "�" => "&iexcl;", "�" => "&cent;", "�" => "&pound;",
        "�" => "&curren;", "�" => "&yen;", "�" => "&brvbar;", "�" => "&sect;",
        "�" => "&uml;", "�" => "&copy;", "�" => "&ordf;", "�" => "&laquo;",
        "�" => "&not;", "�" => "&shy;", "�" => "&reg;", "�" => "&macr;",
        "�" => "&deg;", "�" => "&plusmn;", "�" => "&sup2;", "�" => "&sup3;",
        "�" => "&acute;", "�" => "&micro;", "�" => "&para;", "�" => "&middot;",
        "�" => "&cedil;", "�" => "&sup1;", "�" => "&ordm;", "�" => "&raquo;",
        "�" => "&frac14;", "�" => "&frac12;", "�" => "&frac34;", "�" => "&iquest;",
        "�" => "&Agrave;", "�" => "&Aacute;", "�" => "&Acirc;", "�" => "&Atilde;",
        "�" => "&Auml;", "�" => "&Aring;", "�" => "&AElig;", "�" => "&Ccedil;",
        "�" => "&Egrave;", "�" => "&Eacute;", "�" => "&Ecirc;", "�" => "&Euml;",
        "�" => "&Igrave;", "�" => "&Iacute;", "�" => "&Icirc;", "�" => "&Iuml;",
        "�" => "&ETH;", "�" => "&Ntilde;", "�" => "&Ograve;", "�" => "&Oacute;",
        "�" => "&Ocirc;", "�" => "&Otilde;", "�" => "&Ouml;", "�" => "&times;",
        "�" => "&Oslash;", "�" => "&Ugrave;", "�" => "&Uacute;", "�" => "&Ucirc;",
        "�" => "&Uuml;", "�" => "&Yacute;", "�" => "&THORN;", "�" => "&szlig;",
        "�" => "&agrave;", "�" => "&aacute;", "�" => "&acirc;", "�" => "&atilde;",
        "�" => "&auml;", "�" => "&aring;", "�" => "&aelig;", "�" => "&ccedil;",
        "�" => "&egrave;", "�" => "&eacute;", "�" => "&ecirc;", "�" => "&euml;",
        "�" => "&igrave;", "�" => "&iacute;", "�" => "&icirc;", "�" => "&iuml;",
        "�" => "&eth;", "�" => "&ntilde;", "�" => "&ograve;", "�" => "&oacute;",
        "�" => "&ocirc;", "�" => "&otilde;", "�" => "&ouml;", "�" => "&divide;",
        "�" => "&oslash;", "�" => "&ugrave;", "�" => "&uacute;", "�" => "&ucirc;",
        "�" => "&uuml;", "�" => "&yacute;", "�" => "&thorn;", "�" => "&yuml;",
        "\"" => "&quot;", "'" => "&#39;", "<" => "&lt;", ">" => "&gt;", "&" => "&amp;"
    );

    public static $numericHtml_translation_table = array(
        "�" => "&#160;", "�" => "&#161;", "�" => "&#162;", "�" => "&#163;",
        "�" => "&#164;", "�" => "&#165;", "�" => "&#166;", "�" => "&#167;",
        "�" => "&#168;", "�" => "&#169;", "�" => "&#170;", "�" => "&#171;",
        "�" => "&#172;", "�" => "&#173;", "�" => "&#174;", "�" => "&#175;",
        "�" => "&#176;", "�" => "&#177;", "�" => "&#178;", "�" => "&#179;",
        "�" => "&#180;", "�" => "&#181;", "�" => "&#182;", "�" => "&#183;",
        "�" => "&#184;", "�" => "&#185;", "�" => "&#186;", "�" => "&#187;",
        "�" => "&#188;", "�" => "&#189;", "�" => "&#190;", "�" => "&#191;",
        "�" => "&#192;", "�" => "&#193;", "�" => "&#194;", "�" => "&#195;",
        "�" => "&#196;", "�" => "&#197;", "�" => "&#198;", "�" => "&#199;",
        "�" => "&#200;", "�" => "&#201;", "�" => "&#202;", "�" => "&#203;",
        "�" => "&#204;", "�" => "&#205;", "�" => "&#206;", "�" => "&#207;",
        "�" => "&#208;", "�" => "&#209;", "�" => "&#210;", "�" => "&#211;",
        "�" => "&#212;", "�" => "&#213;", "�" => "&#214;", "�" => "&#215;",
        "�" => "&#216;", "�" => "&#217;", "�" => "&#218;", "�" => "&#219;",
        "�" => "&#220;", "�" => "&#221;", "�" => "&#222;", "�" => "&#223;",
        "�" => "&#224;", "�" => "&#225;", "�" => "&#226;", "�" => "&#227;",
        "�" => "&#228;", "�" => "&#229;", "�" => "&#230;", "�" => "&#231;",
        "�" => "&#232;", "�" => "&#233;", "�" => "&#234;", "�" => "&#235;",
        "�" => "&#236;", "�" => "&#237;", "�" => "&#238;", "�" => "&#239;",
        "�" => "&#240;", "�" => "&#241;", "�" => "&#242;", "�" => "&#243;",
        "�" => "&#244;", "�" => "&#245;", "�" => "&#246;", "�" => "&#247;",
        "�" => "&#248;", "�" => "&#249;", "�" => "&#250;", "�" => "&#251;",
        "�" => "&#252;", "�" => "&#253;", "�" => "&#254;", "�" => "&#255;",
        "\"" => "&#34;", "'" => "&#39;", "<" => "&#60;", ">" => "&#62;",
        "&" => "&#38;"
    );


    // Returns true if $string is valid UTF-8 and false otherwise.
    public static function isUtf8($string)
    {
        if (!is_object($string)) {
            $encode = mb_detect_encoding($string, "UTF-8, ISO-8859-1");
        }
        return ($encode == "UTF-8");
    }

    /**
     * Recode the src about encoding parama
     *
     * @param String $src
     * @param String $encoding
     * @return String, recode src
     */
    public static function recodeSrc($src, $encoding)
    {

        $isUtf8 = self::isUtf8($src . " ");
        if ($src == null) {
            //utf8_encode encode the null value in ''
            $ret = $src;
        } else if ($encoding == \Ximdex\XML\XML::UTF8) {
            if (!$isUtf8) {
                $ret = self::_unicodeToHtmlEntities($src);
                $ret = str_replace('&amp;', 'MAP_GEN_CODE_AMP', $ret);
                $ret = html_entity_decode($ret);
                $ret = str_replace('MAP_GEN_CODE_AMP', '&amp;', $ret);
                $ret = utf8_encode($ret);
            } else {
                //src is UTF-8, we dont do anything
                $ret = $src;
            }
            // default: $encoding == \Ximdex\XML\XML::ISO88591
        } else {
            if ($isUtf8) {
                $ret = utf8_decode($src);
            } else {
                $ret = $src;
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
    public static function recodeFile($name, $encoding)
    {
        $initContent = FsUtils::file_get_contents($name);
        $contentRecode = self::recodeSrc($initContent, $encoding);
        return FsUtils::file_put_contents($name, $contentRecode);
    }

    protected static function _hasHtmlEntities($src)
    {
        $hasEntities = false;
        foreach (self::_html_translation_table as $utf8 => $entity) {
            $numEntity = self::$_numericHtml_translation_table[$utf8];
            $hasEntities = !(strpos($src, $entity) === false) || !(strpos($src, $numEntity) === false);
            if ($hasEntities) {
                break;
            }
        }
        return $hasEntities;
    }

    protected static function _unicodeToHtmlEntities($input)
    {
        $htmlEntities = array_values(self::$html_translation_table);
        return str_replace(self::$numericHtml_translation_table, $htmlEntities, $input);
    }

    // Genera las entidades html
    public static function generateHtmlEntities()
    {
        $ret = '';
        $entities = self::_html_translation_table;
        foreach ($entities as $key => $value) {
            if ($key == '"') {
                $key = str_replace($key, "&#34;", $key);
            }
            $ret .= '<!ENTITY ' . $value . ' "' . $key . '">' . "\n";
        }
        return $ret;
    }

    public static function htmlentitiesWithoutTags($src)
    {
        $ret = '';
        $i = 0;
        for ($i = 0; $i < strlen($src); $i++) {
            //position next start tag
            $posNextTagIni = strpos($src, '<', $i);
            //position next end tag '/>'
            $posNextTagFin = strpos($src, '</', $i);
            //do htmlentities until the position next start tag
            if ($posNextTagIni > $i)
                replace(substr($src, $i, $posNextTagIni - $i), htmlentities(substr($src, $i, $posNextTagIni - $i)), $src);

            //iterator before the next end tag
            $i = $posNextTagFin + 1;
        }

        return $ret;

    }

    public static function encodeSimpleElement($value, $encoding)
    {
        //echo "<h4>encoding the element $value";
        return (self::recodeSrc($value, $encoding));
    }

    public static function encodeArrayElement($array, $encoding)
    {
        foreach ($array as $key => $element) {
            //echo '<h3>Codificando el array</h3>';

            //echo "$key --> $element";
            if (is_array($element)) {
                $array[$key] = self::encodeArrayElement($array[$key], $encoding);
            } else {
                $array[$key] = self::encodeSimpleElement($array[$key], $encoding);
            }
        }
        return $array;
    }

}
