{if (!$core_module)}
<form method="post" name="mg_form" id="mg_form" action="{$_URL_ROOT}/xmd/loadaction.php?action=moduleslist&modsel={$module_name}&method=changeState">



	<p class="states">
		<label><input type="checkbox" name="module_active" {if ($module_actived)} checked="checked" {/if} value="1" /> Actived</label><br />
		<input type="hidden" name="laststate" value="{$module_actived}" />

		<label><input type="checkbox" name="module_install" {if ($module_installed)} checked="checked" {/if} value="1" /> Installed</label>
		<input type="hidden" name="lastinstall" value="{$module_installed}" />
	</p>


	<input type="hidden" name="modsel" id="modsel" value="{$module_name}" />
</form>
{/if}

	</div>

	

</div>
