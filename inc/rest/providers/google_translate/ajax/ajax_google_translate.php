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

?>            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google AJAX Language API - Basic Translation</title>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">

    google.load("language", "1");

<?php


//echo "var text=\"" . urlencode(file_get_contents(realpath(dirname(__FILE__)) . "/../../../content_big.txt")) . "\";";
echo "var text=\"" . urlencode(file_get_contents(realpath(dirname(__FILE__)) . "/../../../content.txt")) . "\";";
?>


    function initialize() {
      google.language.translate(text, "en", "es", function(result) {
        if (!result.error) {
          var container = document.getElementById("translation");
          container.innerHTML = result.translation;
        }
      });
    }
    google.setOnLoadCallback(initialize);

    </script>
  </head>

  <body>
    <div id="translation"></div>
  </body>
</html>
