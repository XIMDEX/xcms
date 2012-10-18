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

<form method="post" name="cln_form" id="cln_form" action="{$action_url}">
	<input type="hidden" name="nodeid" value="{$id_node}" >
	<input type="hidden" name="Name" value="{$name}" >
	<input type="hidden" name="Url" value="{$url}" >
	<input type="hidden" name="Description" value="{$description}" >
	<input type="hidden" name="validated" value="1" >
	<fieldset>
		<legend><span>{t}Enlaces encontrados que apuntan a la misma url{/t}</span></legend>
		{foreach from=$links item=link}
		<ol>
			<li>
				{t}Nombre{/t}: {$link.name}</li>
				<li>{t}Descripción{/t}: {$link.description}
			</li>
		</ol>
		{/foreach}
	</fieldset>
	<fieldset class="buttons-form">
		<ol>
			<li>
				{button label="Cancel" class="close-button"}
				{button label="Continue" class='validate' }<!--message="¿Desea crear el enlace de todas formas?"-->
			</li>
		</ol>
	</fieldset>
</form>
