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

{foreach from=$group['metadata'] key=i item=data}
    {if ($data['type'] == 'image')}
        {$type = 'text'}
    {else}
        {$type = $data['type']}
    {/if}
    {include file="actions/components/form_input.tpl" divClass="small-4{if $i eq count($group['metadata']) - 1} end{/if}"
        title=$data["name"] name="metadata[`$group['id']`][`$data['id']`]" id="metadata[`$group['id']`][`$data['id']`]" type=$type 
        id_node="" value="{$data['value']}" required=$data['required'] readonly=$data['readonly']}
{/foreach}
