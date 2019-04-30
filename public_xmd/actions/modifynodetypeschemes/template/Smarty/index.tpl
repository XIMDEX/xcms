<form method="post" action="{$action_url}">
	{include file="actions/components/title_Description.tpl"}
	<div class="action_content">
	    {foreach from = $schemes item = scheme}
			<fieldset>
				<div class="row tarjeta">
			        <h2 class="h2_general">{$scheme.name}</h2>
	                <div class="metadata_nodetypes_scheme">
	                    {foreach from = $nodeTypes item = nodeType}
	                        <span class="slide-element scheme_nodeType">
	                            <input type="checkbox" class="input-slide" name="nodeTypes[{$scheme.id}][]" value="{$nodeType.id}" 
	                                    {if isset($scheme.nodeTypes[$nodeType.id])} checked="checked" {/if} 
	                                    id="nodeType_{$scheme.id}_{$nodeType.id}" value="{$nodeType.id}" />
	                            <label for="nodeType_{$scheme.id}_{$nodeType.id}" 
	                                   class="label-slide">{t}{$nodeType.description}{/t}</label>
	                        </span>
	                    {foreachelse}
	                       <p>{t}There are not defined node types which can contain metadata{/t}</p>
	                    {/foreach}
	                </div>
			    </div>
			</fieldset>
		{/foreach}
		{if count($schemes) > 0 and count($nodeTypes) > 0}
			<div class="small-12 columns">
				<fieldset class="buttons-form">
		            {button label="Save changes" class="validate btn main_action"}
				</fieldset>
			</div>
		{/if}
	</div>
</form>