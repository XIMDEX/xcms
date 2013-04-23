{include file="./header.tpl"}
		
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras id mauris non dui dapibus mattis eu a justo. Etiam non nibh sit amet sapien faucibus semper. Vestibulum justo justo, varius blandit bibendum et, aliquet id lacus. Aenean accumsan, arcu ac dictum scelerisque, mauris nibh sagittis leo, vitae sollicitudin arcu orci ut dui. Morbi vestibulum, eros at convallis luctus, est nisi dictum sem, sed elementum risus tortor non elit. Suspendisse potenti. Etiam ut eros est, ac dignissim sem. Maecenas at neque odio. Pellentesque vel purus lectus. Curabitur vitae nisi velit, vel ultrices erat. Nam semper, velit ac pharetra porta, sapien erat pulvinar orci, quis lacinia mauris nisl ut eros. Nam et sapien eu lorem porttitor porttitor gravida sit amet enim.</p>

<p>Curabitur non purus sed tellus vulputate auctor. Vivamus ultrices libero vitae arcu vestibulum vehicula. Phasellus diam eros, placerat non dignissim at, pretium venenatis magna. Cras mauris mauris, euismod vitae laoreet sed, rhoncus ac magna. Mauris ac ipsum velit. In hac habitasse platea dictumst. Curabitur eget turpis justo, at adipiscing purus. Quisque tincidunt dui non purus commodo quis gravida diam vestibulum. Integer luctus accumsan libero non mollis. Nam dignissim dolor eget diam euismod sed ullamcorper felis porttitor. Proin nec aliquet eros.</p>


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
