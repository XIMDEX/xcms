{**
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
 *}

<html>
<head>

	<title>{t}Preview{/t}</title>

	<link href="{$_URL_ROOT}/actions/prevdoc/resources/css/prevdoc.css" type="text/css" rel="stylesheet">

{literal}
<script>
function change_title() {
	try {
		var iframe = document.getElementById("prevdoc-document");
		//firefox >= 6 is deprecated document.frames & document.all
		document.title =  iframe.contentWindow.document.title;
	}catch(e){
		//it isnt a ximdoc
	}
}
</script>
{/literal}
</head>
<body>
	<fieldset class="prevdoc-container">
		<legend><span>{t}Preview{/t}</span></legend>
		<iframe class="prevdoc-document" id="prevdoc-document" name="prevdoc-document"  src="{$prevUrl}" onload="change_title()" />
	</fieldset>
</body>
</html>