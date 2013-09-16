{if (null != $templates)}
<h2>{t}Removed templates{/t}</h2>
<fieldset>
	 <ul class="files_ok">
		{section name=i loop=$templates}
		<li><strong>{$templates[i].Name}:</strong> {if ($templates[i].Result)}{t}ok{/t}{else}{t}fail{/t}{/if}</li>
		{/section}
	 </ul>
</fieldset>
{/if}
