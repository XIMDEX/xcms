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
<html ng-app="ximdex" ng-strict-di lang="{$user_locale['Lang']}">
<head>
    <title>{$xinversion}</title>
    <meta charset="UTF-8">
    <!-- disable zoom -->
    <meta content='maximum-scale=1.0, user-scalable=0' name='viewport' />
    <!-- constant css includes -->
    <!--link id="main_css" type="text/css" href="{$_URL_ROOT}/xmd/style/jquery/{$theme}/jquery-ui-1.8.2.custom.css" rel="stylesheet" /-->
    <link rel="icon" href="{$_URL_ROOT}/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="{$_URL_ROOT}/favicon.ico" type="image/x-icon"/>

    {foreach from=$css_files key=id item=href}
        <link type="text/css" href="{$href}" rel="stylesheet"/>
    {/foreach}

    <!-- css widgets -->
    %=css_widgets%

    <!-- constant js includes -->
    <script type="text/javascript" src="{$_URL_ROOT}/extensions//ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="{$_URL_ROOT}/xmd/js/vars_js.php?id={$time_id}"></script>

    {foreach from=$js_files key=id item=src}
        <script type="text/javascript" src="{$src}"></script>
    {/foreach}
    <script type="text/javascript" src="{$_URL_ROOT}/inc/i18n/locale/{$user_locale.Code}/LC_MESSAGES/strings.js"
            charset="UTF-8"></script>
    <!-- js widgets -->
    %=js_widgets%
</head>
<body ng-controller="XMainCtrl" ng-keydown="keydown($event)" ng-keyup="keyup($event)">
<div id="angular-event-relay"></div>
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

<searchpanel include="yes"/>

<div id="header">
    <h1><img src="{$_URL_ROOT}/xmd/images/header/logo_xim.png" border="0" alt="{t}Ximdex logotype{/t}"
             title="{t}Semantic content management with Ximdex{/t}" id="logo"/></h1>

    <div class="session-info">
        <!-- <img class="login-img" src="{$_URL_ROOT}/xmd/images/user_48.png" border="0" alt="Login" title="Login"/>-->
        <div class="language">
            <div class="menu-header">
                <span class="current-language icon">{$user_locale.Lang}</span>
                <span class="login-name session-info-text">{$loginName}</span>
            </div>
            <div ng-controller="XUserMenuCtrl" class="user-menu">
                <ul>
                    <li class="icon language-icon">
                        <a>{t}Language{/t}</a>
                        <ul class="selector_language">
                            {section name=i loop=$locales}
                                <li ng-click="changeLang('{$locales[i].Code}','{$locales[i].Name|gettext} ({$locales[i].Lang})')" {if ($user_locale.ID == $locales[i].ID || ( null == $user_locale && $locales[i].ID == $smarty.const.DEFAULT_LOCALE)  )} class="selected icon" {/if}>{$locales[i].Name|gettext}
                                    ({$locales[i].Lang})
                                </li>
                            {/section}
                        </ul>
                    </li>
                    <li class="icon config" ng-click="modifyAccount('{$userID}', '{t}Modify your account{/t}')">
                        <a >{t}Modify your account{/t}</a>
                    </li>
                    <li class="icon help">
                        <a target="_blank" href="https://github.com/XIMDEX/ximdex/wiki">{t}Help{/t}</a>
                    </li>
                    <li class="icon contact">
                        <a target="_blank" href="mailto:help@ximdex.org">{t}Contact us{/t}</a>
                    </li>
                    <li class="icon logout">
                        <a href="{$_URL_ROOT}?action=logout">{t}Logout{/t}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <spotlight id="mini-spotlight"/>
</div>
<div id="body">
    <div id="bw1" class="browser-window">
        <div class="browser-window-content">
            <div class="hbox browser-hbox">
                <xim-browser xim-id="angular-tree" xim-mode="sidebar"></xim-browser>
                <xim-tabs xim-id="angular-content"></xim-tabs>
            </div>
        </div>
    </div>
</div>

</body>
</html>
