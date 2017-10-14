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

<form method="post" name="ca_form" id="ca_form"  action="{$action_url}">
	<fieldset>
	<legend><span>{t}Image browser{/t}</span></legend>
		<input type="hidden" name="nodeid" value="{$id_node}"/>

<p>{t}Select the way you want to explore the image batch:{/t}<span id="typeView"><input type="radio" name="grupoforma" value="lista" onclick="changeViewR(this);" checked />Lista<input type="radio" name="grupoforma" value="miniaturas" onclick="changeViewR(this);" />Miniaturas</span></p>
<br/>
<p>{t}Select the image batch to explore:{/t}</p>
<br/>
<p>{t}Preview{/t}</p>
<div id="area1" style="overflow:auto;height:120px;">1</div>
<div id="area2" style="overflow:auto;height:120px;">2</div>
<div id="area3" style="overflow:auto;height:120px;">3</div>

<p>{t}Select the zoom factor in which you want to view the images{/t}
	<span id="selectScale">
		<input type="radio" name="grupozoom" value="0.1" onclick="changeScaleR(this);" />10%
		<input type="radio" name="grupozoom" value="0.25" onclick="changeScaleR(this);" />25%
		<input type="radio" name="grupozoom" value="0.5" onclick="changeScaleR(this);" checked />50%
		<input type="radio" name="grupozoom" value="0.75" onclick="changeScaleR(this);" />75%
	</span>
</p>

<!--		<ol>
			<li>
				<label for="plantilla">{t}Template{/t}</label>
				<select name="template" id="plantilla" class="validable not_empty">
					<option value="">{t}Select template{/t}</option>
					{foreach from=$templates key=ene item=template}
						<option value="{$template.id}">{$template.name}</option>
					{/foreach}
				</select>
			</li>
			<li>
				<label>{t}Image batch{/t}</label>
				<select id="nodoIDF" name="loteid" class="caja">
					<option value="0">(none)</option>
					{foreach from=$lotes key=ene item=lote}
						<option value="{$lote.IdNode}">{$lote.Name}</option>
					{/foreach}
				</select>
			</li>
		</ol>-->
	</fieldset>
</form>
