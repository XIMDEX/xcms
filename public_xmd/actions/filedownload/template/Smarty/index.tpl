{**
 *  \details &copy; 2019 Open Ximdex Evolution SL [http://www.ximdex.org]
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

{include file="actions/components/title_Description.tpl"}
<div class="action_content">
	<div class="row tarjeta">
		<div class="small-12 columns title_tarjeta">
			<h2 class="h2_general">
                {t}Download file{/t}
                {if $version !== null}
                    &nbsp;({t}version{/t} {$version}.{$subversion})
                {/if}
			</h2>
		</div>
		<div class="small-12 columns">
			<div class="alert alert-info">
				<strong>Info!</strong> {t}File download will start immediately. If it does not start, click on:{/t} 
			    <a href="{url}/?action=filedownload&method=downloadFile&nodeid={$id_node}{/url}&version={$version}&subversion={$subversion}" 
                        class="destacada download_link" title="{t nodename=$node_name}Download %1{/t}">{$node_name}</a>.
			</div>
		</div>
		<!--
		<div class="small-12 columns">
            <fieldset class="buttons-form">
                {button label="Download" class="button-download btn main_action"}
            </fieldset>
		</div>
		-->
    </div>
</div>