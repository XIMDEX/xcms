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


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

    <title>{t}Ximdex Profiler{/t}</title>

    <!--[if IE]><script language="javascript" type="text/javascript" src="{EXTENSIONS::Jquery_path()}/plugins/jquery.flot/excanvas.pack.js"></script><![endif]-->
	{foreach from=$js_files item=js_file}
	<script type="text/javascript" src="{$js_file}"></script>
	{/foreach}
	{foreach from=$css_files item=css_file}
	<link rel="stylesheet" type="text/css" href="{$css_file}" >
	{/foreach}

    <script language="javascript" type="text/javascript">

    </script>
</head>
<body>

<div>
Graphs: <select id="dropDownGraphs"></select>
&nbsp;<button id="refresh">{t}Refresh{/t}</button>
<input id="chkShowGrid" type="checkbox" /><label id="chkShowGrid_label" for="chkShowGrid">{t}Show Grid{/t}</label>
<!--
<Function: <select id="dropFunctions"></select>
Class: <select id="dropClasses"></select>

<input id="chkShowAvarage" type="checkbox">Show Avarage</input>
<input id="txtTests" type="text" value="*" />
</div>
-->

<h3 id="testInfo" class="label"></h3>
<h4 id="methodInfo" class="label"></h4>
<div id="chart" class="chart"></div>
<div id="grid" class="chart"></div>

</body>
</html>
