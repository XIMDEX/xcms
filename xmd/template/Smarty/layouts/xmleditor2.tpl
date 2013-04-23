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

<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

	<title>Xedit - {$xinversion}</title>

	{foreach from=$base_tags item=base}
	<base href="{$base}"/>
	{/foreach}

	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

	<link rel="icon" href="../../../favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon" />

	{foreach from=$css_files key=id item=href}
		<link type="text/css" href="{$href}" rel="stylesheet" />
	{/foreach}

	<!-- css widgets -->
	%=css_widgets%

	<!-- constant js includes -->
	<script type="text/javascript" src="{$_URL_ROOT}/xmd/js/vars_js.php?id={$time_id}"></script>
	{if ($user_connect != NULL)}
			<script type="text/javascript" src="{$_URL_ROOT}{$user_connect}"></script>
	{/if}

	{foreach from=$js_files key=id item=src}
		<script type="text/javascript" src="{$src}"></script>
	{/foreach}

	<!-- js widgets -->
	%=js_widgets%

	<script language="javascript" type="text/javascript">

		{literal}
		window.kupu = null;
		//window.kupuToolHandlers = [];
		$(document).ready(function() {

			fixJQueryGetters($);

		{/literal}
			{$on_load_functions}
		{literal}
		});
		{/literal}

	</script>

</head>

	<browserwindow include="yes" />
	<searchpanel include="yes" />
	<listview include="yes" />
	<treeview include="yes" />

	<body onresize="{$on_resize_functions}">

	<menubar id="xedit_menubar" include="yes" />
	<!--buttonbar id="xedit_buttonbar_controls" /-->
	<!--buttonbar id="xedit_buttonbar_rngelements" /-->
	<!--toolbox id="tb1" handler="AttributesToolbox_Loader" /-->

	{include file="$_ACTION_CONTROLLER"}

	</body>
</html>
