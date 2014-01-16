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
	<div class="action_header">
<h2>{t}Search results{/t}</h2>
</div>
		<div class="action_content">

<fieldset>


	<input type="hidden" name="stringsearch" value="{$stringsearch}" />

		<div class="result_info row-item">
			<span class="result_name">
				name
			</span>
			<span class="result_url">
				http://dribbble.com/shots/475183-Flight-search-results
			</span>
			<div class="row-item-actions">			
			<div class="description_btn">
				<span class="result_description  icon btn-unlabel-rounded">
					<span class="tooltip">
						<p>Description</p>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, ad, atque, nam a sit fugit officiis officia possimus enim voluptates consectetur veritatis deserunt magni beatae quam quae hic iure voluptatibus.</p>
					</span>
				</span>
			</div>
			<a href="" class="icon btn-unlabel-rounded not_checked "><span>Not checked</span></a>
			</div>
		</div>

	{*<listview handler="myhandler" class="xim-listview-results" useXVFS="no"
		showBrowser="false" nodeid="{$id_node}" actionid="{$id_action}"
		rec="{$rec}" all="{$all}" field="{$field}" criteria="{$criteria}" />

	<paginator class="links-paginator" />*}

</fieldset>


</div>

<fieldset class="buttons-form positioned_btn">
	{button label="Go back" type="goback" class="btn"}
</fieldset>