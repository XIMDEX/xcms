<h2>{t}Information{/t}</h2>
<fieldset class="information">
	<ul>
	<li><strong>{t}NodeId{/t}:</strong> {$info.nodeid}</li>
	<li><strong>{t}Name{/t}:</strong> {$info.name}</li>
	<li><strong>{t}NodeType{/t}:</strong> {$info.typename} ({$info.type}) </strong></li>
	<li><strong>{t}Path{/t}:</strong> {$info.path} </strong></li>
	<li><strong>{t}Parent node{/t}:</strong> {$info.parent}</li>
	{if ($info.date)}
		<li><strong>{t}State{/t}:</strong>  {if ($info.published)}{t}Published{/t}{else}{t}Not published{/t}{/if} </strong></li>
		<li><strong>{t}Last version{/t}:</strong> {$info.version}.{$info.subversion} </strong></li>
		<li><strong>{t}Last modified{/t}:</strong> {$info.date|date_format:"%d/%m/%y %H:%S"} </strong></li>
		<li><strong>{t}Last modified by{/t}:</strong> {$info.lastusername} ( {$info.lastuser} ) </strong></li>
	{/if}
	<li><strong>{t}Languages{/t}:</strong>
	{if ($languages)}
		{section name=i loop=$languages}
					{$languages[i].Name} ( {$languages[i].Id} )
			{if (!$smarty.section.i.last)},{/if}
			{if (0 == $smarty.section.i.index_next%4  )}<br />{/if}
		{/section}
		</li>
	{else}
		{t}No found{/t}
	{/if}
	<li><strong>{t}Channels{/t}:</strong>
	{section name=i loop=$channels}
				{$channels[i].Name} ( {$channels[i].IdChannel} )
		{if (!$smarty.section.i.last)},{/if}
		{if (0 == $smarty.section.i.index_next%4  )}<br />{/if}
	{/section}
	</li>
	<li><strong>{t}Transformer{/t}:</strong> {$transformer[0]}</li>
	<li><strong>{t}Actions avaliable{/t}:</strong>
	{section name=i loop=$actions}
		{$actions[i].name} <!--( {$actions[i].actionid},  {$actions[i].command}  )-->
		{if (!$smarty.section.i.last)},
		{if (0 == $smarty.section.i.index_next%4  )}<br />{/if}
		{else}.
		{/if}
	{/section}
		</li>
	</ul>
</fieldset>
