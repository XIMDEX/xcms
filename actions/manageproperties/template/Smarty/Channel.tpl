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


{assign var="channels" value=$properties.Channel}

<fieldset>

<legend><span>{t}Channels{/t}</span></legend>
<div class="manageproperties">

	<div class="xright-block">
		<input type="radio" name="inherited_channels" class="channels_inherited"
			value="inherited" {if $Channel_inherited == 'inherited'}checked{/if} />
		<label>{t}Use inherited channels{/t}</label>
		<ol>
			<li>
			{foreach from=$channels item=channel}
				{$channel.Name},
			{/foreach}
			</li>
		</ol>
	</div>

	<div class="xright-block">

		<div>
			<input type="radio" name="inherited_channels" class="channels_overwritten"
				value="overwrite" {if $Channel_inherited == 'overwrite'}checked{/if} />
			<label>{t}Overwrite inherited channels{/t}</label>
		</div>

		<div class="left-block">
			<ol>
				{if ($channels)}
				{foreach from=$channels item=channel}
				<li>
					<input
						type="checkbox"
						class="channels"
						name="Channel[]"
						value="{$channel.IdChannel}"
						{if ($channel.Checked == 1)}
							checked="{$channel.Checked}"
						{/if}
						{if $Channel_inherited == 'inherited'}
							disabled
						{/if}
						/>
					{$channel.Name}
				</li>
				{/foreach}
				{/if}
			</ol>
		</div>

		<div class="right-block">
			<ol>
				{if ($channels)}
				{foreach from=$channels item=channel}
				<li>
					<div class="novisible recursive channels_recursive_{$channel.IdChannel}">
						<input
							type="checkbox"
							class="channels_recursive"
							name="Channel_recursive[]"
							value="{$channel.IdChannel}"
							/>
						{t}Associate channel recursively with all documents.{/t}
					</div>
					<div class="{if ($channel.Checked != 1)}novisible{/if} apply">
						<button
							type="button"
							class="channels_apply channels_apply_{$channel.IdChannel}"
							name="Channel_apply"
							value="{$channel.IdChannel}">
						{t}Associate{/t}
						</button>
					</div>
				</li>
				{/foreach}
				{/if}
			</ol>
		</div>

	</div>
</div>

</fieldset>
