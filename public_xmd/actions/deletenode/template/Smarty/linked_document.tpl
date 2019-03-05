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

<div class="action_header">
    <h2>{t}You can't delete the node{/t}</h2>
    <fieldset class="buttons-form">
        {button class="close-button btn main_action" label="Close"}
    </fieldset>
</div>
<div class="message-warning message">
    <p>{t}This document has a symbolic link{/t}.</p>
</div>
<div class="action_content">
    <p>{t}To delete this node you have to break the link with the following node(s){/t}:</p>
    {foreach from=$path_symbolics item=path_symbolic}
        <p><strong>{$path_symbolic}</strong></p>
    {/foreach}
</div>
