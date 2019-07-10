<div class="action_header">
	<h2>{t}Removed templates{/t}</h2>
</div>
<div class="action_content">
{if (null != $templates)}
<p>{t}Results{/t}:</p>
<fieldset>
	 <ul class="files_ok">
		{section name=i loop=$templates}
		<li><strong>{$templates[i].Name}:</strong> {if ($templates[i].Result)}{t}ok{/t}{else}{t}fail{/t}{/if}</li>
		{/section}
	 </ul>
</fieldset>
{else}
<p>{t}There aren't any selected template{/t}</p>
{/if}
</div>