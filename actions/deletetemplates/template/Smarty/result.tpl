{if (null != $templates)}
<fieldset>
	 <legend><span>{t}Removed templates{/t}</span></legend>
	 <ul class="files_ok">
		{section name=i loop=$templates}
		<li><strong>{$templates[i].Name}:</strong> {if ($templates[i].Result)}{t}ok{/t}{else}{t}fail{/t}{/if}</li>
		{/section}
	 </ul>
</fieldset>
{/if}
