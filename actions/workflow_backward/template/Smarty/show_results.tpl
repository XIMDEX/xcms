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
    <h2>{t}Previous state{/t}</h2>
    <fieldset class="buttons-form">
        {if ($goback) }
			{button class="goback-button  btn main_action" label="Go back"}
		{else}
			{button class="close-button btn" label="Close"}
		{/if}
    </fieldset>
</div>
{if (count($messages)) }
    {foreach name=messages from=$messages key=message_id item=message}
        <div class="message {if $message["type"]==2}message-success{elseif $message["type"]==1}message-warning{else}message-error{/if}">
            <p class="ui-icon-notice">{$message.message}</p>
        </div>
    {/foreach}
{/if}



