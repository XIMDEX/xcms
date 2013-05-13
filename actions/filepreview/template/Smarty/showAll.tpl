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
 
{if isset($mesg)}
     <div class=''>{$mesg}</div>
{else}
    <ul id='image_container' class='image_container'>
    {foreach from=$imageNodes name=imagenodes item=imagenode}
        <li><img src='{$imagenode.src}' title='{$imagenode.name}' data-nodeid='{$imagenode.idnode}' data-width='{$imagenode.width}' data-height='{$imagenode.height}' data-dimensions='{$imagenode.dimensions}' data-original_path='{$imagenode.original_path}' data-mime='{$imagenode.mime}' data-size='{$imagenode.size}'/></li>
    {/foreach}
    </div>
{/if}
