<form method="post" id="up_form" name="up_form" action="{$action_url}" enctype="multipart/form-data">
	<input type="hidden" name="nodeid" value="{$id_node}" />
	{include file="actions/components/title_Description.tpl"}
	<div class="action_content">
        <div class="row tarjeta">
            <div class="small-12 columns title_tarjeta">
                <h2 class="h2_general">{t}Replace file{/t}</h2>
            </div>
            <div class="small-12 columns">
                <div class="input">
                    <fieldset>
                        <p>
                            <label for="upload" class="aligned">{t}Select the new file to replace{/t} (Max {$maxSize}):</label>
                        </p>
                        <input type="file" name="upload" id="upload" class="cajaxg validable not_empty" />
                    </fieldset>
                </div>
            </div>
           <div class="small-12 columns">
                <fieldset class="buttons-form">
                    <input class="btn" type="submit" value="{t}Replace file{/t}" />
                </fieldset>
            </div>
        </div>
	</div>
</form>