{include file="./header.tpl"}
		
<p><strong>ximLOADER module</strong> provides a list of demo projects that the user can install to see and understand how Ximdex CMS works.</p>

<p>It's one of the modules suggested during the installation process.</p>

<form method="post" name="mg_form" id="mg_form" action="{$_URL_ROOT}/xmd/loadaction.php?action=moduleslist&modsel={$module_name}&method=changeState">

	<p class="states">

		<input type="hidden" name="laststate" value="{$module_actived}" />

		<label><input type="checkbox" name="module_install" {if ($module_installed)} checked="checked" {/if} value="1" /> Installed</label>
		<input type="hidden" name="lastinstall" value="{$module_installed}" />
	</p>



	<input type="hidden" name="modsel" id="modsel" value="{$module_name}" />
</form>


	</div>

	

</div>
