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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>

		<title>{$xinversion}</title>

		<!-- constant css includes -->
		<!--link id="main_css" type="text/css" href="{$_URL_ROOT}/xmd/style/jquery/{$theme}/jquery-ui-1.8.2.custom.css" rel="stylesheet" /-->
		<link rel="icon" href="{$_URL_ROOT}/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="{$_URL_ROOT}/favicon.ico" type="image/x-icon" />

		{foreach from=$css_files key=id item=href}
			<link type="text/css" href="{$href}" rel="stylesheet" />
		{/foreach}
		<link href='http://fonts.googleapis.com/css?family=Coustard:400,900' rel='stylesheet' type='text/css'>
		<!-- css widgets -->
		%=css_widgets%

		<!-- constant js includes -->
      <script type="text/javascript" src="{$_URL_ROOT}/extensions//ckeditor/ckeditor.js"></script>
		<script type="text/javascript" src="{$_URL_ROOT}/xmd/js/vars_js.php?id={$time_id}"></script>

		{foreach from=$js_files key=id item=src}
			<script type="text/javascript" src="{$src}"></script>
		{/foreach}

		<!-- js widgets -->
		%=js_widgets%
	</head>
	<body>

		<div id="ximdex-splash">

			<div class="loading"><p>{t}Loading...{/t}</p><span class="progress">&nbsp;</span></div>
			<div class="ximdex_splash_content">
				{if ($splash_content != null)}
					{$splash_content}
				{else}
					{include file="$splash_file"}
				{/if}
			</div>
		</div>

		<tabs include="yes" />
		<searchpanel include="yes" />
		<hbox include="yes" />

		<div id="header">
           <h1><img src="{$_URL_ROOT}/xmd/images/header/logo_xim.png" border="0" alt="{t}Ximdex logotype{/t}" title="{t}Semantic content management with Ximdex{/t}" id="logo" /></h1>
            <div class="session-info">
	           <!-- <img class="login-img" src="{$_URL_ROOT}/xmd/images/user_48.png" border="0" alt="Login" title="Login"/>-->
	            <div class="language"><span class="current">{$user_locale.Lang}</span>
	            <div class="selector_language">
	            	<ul>
							{section name=i loop=$locales}
							<li {if ($user_locale.ID == $locales[i].ID || ( null == $user_locale && $locales[i].ID == $smarty.const.DEFAULT_LOCALE)  )} class="selected" {/if}>{$locales[i].Name|gettext} ({$locales[i].Lang})
							<input type="hidden" name="language" value="{$locales[i].Code}" />
							</li>
							{/section}
	            	</ul>
	            </div>
	            	<span class="session-info-text login-name">{$loginName}</span>
	            </div>
	            <a class="session-info-text session-logout" href="{$_URL_ROOT}/xmd/loadaction.php?action=logout">{t}Logout{/t}</a>
            </div>
			<spotlight id="mini-spotlight" />
		</div>
		<div id="body">

			<browserwindow id="bw1" />

		</div>

	</body>
</html>
