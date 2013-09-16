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
       	<h2 class="action">{t}Create project{/t}</h2>
       	<fieldset class="buttons-form">
		<!--{button type="reset" label='Reset' class='btn' }-->
		{button label="Create project" class='validate btn main_action' }
	</fieldset>
       </div>
<div class="action_content">
	<form method="post" id="mu_action" action="{$action_url}">
		<label for="name" class="">{t}Name{/t}</label>
					<input type="text" name="name" id="name" value="{$name}" class='cajaxg validable not_empty long'/>
			<a href="#" class="advanced-btn">Advanced settings</a>
			<div class="advanced-settings">{t}Publication channels{/t}
					{foreach from=$channels key=index item=channelData }
					        <span>
					          <input type="checkbox" class="validable canales check_group__canales"
										name="channels_listed[{$channelData.id}]" id="p_{$channelData.id}" />
					         <label for="p_{$channelData.id}" class="inline nofloat">{$channelData.name}</label> </span>
					        <!--	<img class="xim-treeview-icon icon-channel"/> The path has been deleted and the icon just is show by CSS, src="{$_URL_ROOT}/xmd/images/icons/channel.png" -->
					        {foreachelse}
					        <p class="message_warning">{t}There are no channels created in the system{/t}</p>
					        {/foreach}</div>


			<label for="theme">{t}Theme{/t}</label>
			<ul class="themes">
				{foreach from=$themes key=index item=theme}
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/themes/{$theme.name}/{$theme.name}.png" alt="{$theme.title}">
					<div class="actions"><a href="" class="icon select" data-theme="blank">Select</a><a data-theme="blank" href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">{$theme.title}</p>
					<p class="type">{$theme.description}</p>
				</li>
				{/foreach}
			<!--	<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/cbdoc/cbdoc.png" alt="Cbdoc theme">
					<div class="actions"><a href="" class="icon select" data-theme="cbdoc">Select</a><a href="" class="icon custom" data-theme="cbdoc">Custom</a></div>
					</div>
					<p class="title">CBDoc</p>
					<p class="type">Single page with scroll</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/geuin/geuin.png" alt="Geuin theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Geuin</p>
					<p class="type">Portal completo con un nivel de navegación</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/ximdex/ximdex_theme.png" alt="Ximdex theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Ximdex</p>
					<p class="type">Portal completo con varios niveles de navegación</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/blank/blank_theme.png" alt="Blank theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Tema en blanco</p>
					<p class="type">Tema base para construir desde cero</p>
				</li>
				<li class="theme">
						<div class="img_container">
						<img src="modules/XSparrow/Templates/ximdex/ximdex_theme.png" alt="Ximdex theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Ximdex</p>
					<p class="type">Portal completo con varios niveles de navegación</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/cbdoc/cbdoc.png" alt="Cbdoc theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">CBDoc</p>
					<p class="type">Single page with scroll</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/geuin/geuin.png" alt="Geuin theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Geuin</p>
					<p class="type">Portal completo con un nivel de navegación</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/cbdoc/cbdoc.png" alt="Cbdoc theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">CBDoc</p>
					<p class="type">Single page with scroll</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/geuin/geuin.png" alt="Geuin theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Geuin</p>
					<p class="type">Portal completo con un nivel de navegación</p>
				</li>
				<li class="theme">
					<div class="img_container">
						<img src="modules/XSparrow/Templates/cbdoc/cbdoc.png" alt="Cbdoc theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">CBDoc</p>
					<p class="type">Single page with scroll</p>
				</li>
				<li class="theme">
						<div class="img_container">
						<img src="modules/XSparrow/Templates/ximdex/ximdex_theme.png" alt="Ximdex theme">
					<div class="actions"><a href="" class="icon select">Select</a><a href="" class="icon custom">Custom</a></div>
					</div>
					<p class="title">Ximdex</p>
					<p class="type">Portal completo con varios niveles de navegación</p>
				</li>-->
			</ul>

	</form>

	        <div class="customize-template-form">
			<legend><span>{t}Create bootstrap project{/t}</span></legend>
			<ol style="width:50%; float:left">
				<li>
					<label for="name" class="aligned">{t}Name(No spaces, please){/t}</label>
					<input type="text" name="name" id="name" value="{$name}" class='cajaxg validable not_empty'/>
				</li>
					<li>
					<label for="name" class="aligned">{t}Name(No spaces, please){/t}</label>
					<input type="text" name="name" id="name" value="{$name}" class='cajaxg validable not_empty'/>
				</li>
				<li>
	                               <label for="name" class="aligned">{t}Web's Title{/t}</label>
	                               <input type="text" name="title" id="title" value="{$name}" class='cajaxg validable not_empty'/>

				</li>
				<li>
					<label for="email" class="aligned">{t}Principal color{/t}</label>
					<input type="text" name="principal_color" id="principal_color" value="#006B6C" class='cajaxg validable not_empty input_colorpicker'/>
				</li>
				<li>
					<label for="password_" class="aligned">{t}Secundary color{/t}</label>
					<input type="text" name='secundary_color' id="secundary_color" value="#ffffff" class='caja validable not_empty input_colorpicker'/>
				</li>
				<li>
					<label for="password_repeated" class="aligned">{t}Font color{/t}</label>
					<input type="text" name='font_color' id="font_color" value="#000000" class='caja validable not_empty input_colorpicker'/>
				</li>
			</ol>
			<div class="bsPreviewContainer" style="width:40%; float:left; clear:right; padding:10px; background-color:#006B6C">
				<div style="min-height:20px; padding:10px; text-align:right;background-color:#ffffff" class="bsPreviewTitle">
					<h4>Title</h4>
				</div>
				<div class="bsPreviewContent" style="padding:10px; color:#000000">
					A Sample text to show the color text.
					Use the form to change the color.
					You will can change all of this in config node, in ximlet folder.
				</div>
			</div>
		</div>
</div>
