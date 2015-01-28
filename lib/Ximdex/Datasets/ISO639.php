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

namespace Ximdex\Datasets ;


class ISO639 {
    static $languages = array(
        'af' => 'afrikaans',
        'id' => 'bahasa',
        'eu' => 'basque',
        'br' => 'breton',
        'bg' => 'bulgarian',
        'ca' => 'catalan',
        'hr' => 'croatian',
        'cs' => 'czech',
        'da' => 'danish',
        'nl' => 'dutch',
        'en' => 'english',
        'eo' => 'esperanto',
        'et' => 'estonian',
        'fi' => 'finnish',
        'fr' => 'french' ,
        'gl' => 'galician',
        'de' => 'german',
        'el' => 'greek',
        'he' => 'hebrew',
        'hu' => 'hungarian',
        'is' => 'icelandic',
        'ia' => 'interlingua',
        'ga' => 'irish',
        'it' => 'italian',
        'la' => 'latin',
        'nb' => 'norsk',
        'pl' => 'polish',
        'pt' => 'portuges',
        'ro' => 'romanian',
        'ru' => 'russian',
        'gd' => 'scottish',
        'es' => 'spanish',
        'sk' => 'slovak',
        'sl' => 'slovene',
        'sv' => 'swedish',
        'sr' => 'serbian',
        'tr' => 'turkish',
        'uk' => 'ukrainian',
        'cy' => 'welsh'
    );
    /**
     *
     * @param $code
     * @return string
     */
    public static function getName($code ) {
        if (isset( self::$languages[$code] ) ) {
            return self::$languages[$code];
        }
        else {
            return null;
        }
    }
    /**
     *
     * @param $name
     * @return string
     */
    public static function getCode($name) {
        $langAux = array_flip( self::$languages );
        if (isset( $langAux[$name] ) ) {
            return $langAux[$name];
        }
        else {
            return null;
        }
    }
}