{if (!$db_connection)}
	{t}Please, check your database configuration in the /conf/install_params.conf.php file.{/t}
{else}
	{t}ERROR: Ximdex is not configured. Please run installer.{/t}
{/if}
