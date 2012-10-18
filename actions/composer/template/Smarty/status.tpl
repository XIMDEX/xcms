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
	<title>{t}Status bar{/t}</title>
<link rel="stylesheet" type="text/css" href="style/estilo_barra-estado.css">
<script type="text/javascript" src="{$_URL_ROOT}{$base_action}resources/js/status.js"></script> 

</head>
<body  ONLOAD="clock()">
<table width="100%" cellpadding="0" cellspacing="0" border="0" id='table_status' >
	<tr>
		<td class="boxes">

			<table cellpadding=0 cellspacing=0 width="100%" border=0 class="coolBar" nowrap>
				<tr>
					<td width="40" align="right" valign="middle" id='usuario'>
						&nbsp;&nbsp;&nbsp;{t}User{/t}:&nbsp;
					</td>

					<td width="225" valign="middle" style="overflow: hidden; ">
						<div onfocus="this.blur()" id='user_name' id='user_name'>{$user_name|gettext}</div>
					</td>

					<td width="15" align="right" valign="middle" id='ip'>
						&nbsp;&nbsp;&nbsp;{t}IP{/t}:&nbsp;
					</td>

					<td width="100" valign="middle" style="overflow: hidden; ">
						<div onfocus="this.blur()" id='ip_address'>{$ip_address}</div>
					</td>

					<td valign="middle" align="right" id='td_autoalert' >
						<iframe application="yes" id='autoalert' src="{$composer_index}?method=autoalert&alert=text" style="" frameborder="0" scrolling="no"></iframe>
					</td>
				</tr>
			</table>

		</td>


		<td width="2"><div></div></td>


		<td class="boxes" width="30" align="center">
			<iframe application="yes" src="{$composer_index}?method=autoalert&alert=image" style="width: 16px; height: 16px;" frameborder="0" scrolling="no"></iframe>
		</td>


		<td width="2"><div></div></td>


		<td class="boxes" width="78" valign="middle" nowrap>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="middle"><img src="images/clock.png" hspace="2" class="clockicon" ONCLICK="time_elapsed();"></td>
				<td valign="middle"><DIV CLASS="clock" ONFOCUS="this.blur();" ONCLICK="time_elapsed();" TYPE="text" ID="time"></div></td>
				<td valign="bottom"><img src="images/corner.png"></td>
			</tr>

		</table>
		</td>
	</tr>
</table>
</body>
</html>