{if (!$db_connection)}
	{t}Pleaese, check database configuration in your /conf/install_params.conf.php{/t}
{else}
	{t}ERROR: Ximdex is not configured. Please run installer.{/t}
{/if}