<div class="subfolders-available col2-3">
	<h3>{t}Subfolders availables{/t}</h3>
        {if $subfolders|@count != 0}
        	{foreach from=$subfolders key=nt item=foldername}
                <div class="subfolder box-col1-1">
                	<input name="folderlst[]" type="checkbox" value="{$nt}" {if $nt eq 5018 || $nt eq 5016 || $nt eq 5022 || $nt eq 5301 || $nt eq 5304} checked{/if} {if $nt eq 5301 || $nt eq 5304} readonly {/if} class="hidden-focus" id="{$nt}"/>
                        <label class="icon" for="{$nt}"><strong class="icon {$foldername[0]}">{$foldername[0]}</strong></label>
                        <span class="info">{t}{$foldername[1]}{/t}</span>
                </div>      
        	{/foreach}  
	{else}      
        	<p>{t}There aren't any avaliable subfolders for this section.{/t}</p>
	{/if}       
</div>
