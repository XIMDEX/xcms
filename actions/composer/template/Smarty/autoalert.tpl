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
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<META http-equiv=Refresh content="60">
<link rel="stylesheet" type="text/css" href="{$_URL_ROOT}{$base_action}resources/css/autoalert.css">
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
	<tr>
		<td align="center" valign="middle" id='td_autoalert'>
		<a class="" href="javascript://" onclick="parent.parent.frames.toolbar.MailModule()" style="">
		{if ($tipo == 'text')}
			{$nummsg}
			{if ($nummsg == 1)}
				{t}New message{/t}
			{else}
				{t}New messages{/t}

			{/if}
		{else if($tipo == 'image' && $nummsg}
			<img src='./images/botones/no_leido.gif' border='0'>
		{/if}
		</a>
		</td>
	</tr>
</table>
</body>
</html>

