
<form method="post" name="m_form" id="m_form" action="{$action_url}" class='validate_ajax'>
        <input type="hidden" name='nodeid' value="{$nodeid}" />
        {include file="actions/components/title_Description.tpl"}
        <div class="action_content">
                <div class="row tarjeta">
                        <div class="small-12 columns title_tarjeta">
                                <h2 class="h2_general">{t}Set Metadata{/t}</h2>
                        </div>

                        {include file="actions/components/form_select.tpl"
                                divClass="small-12 "
                                title="Groups" name="idGroup" id="idGroup"
                                optionTitle="Select a group" items=$metagroups required=true}

                        <div id="metadataLoad">
                                {include file="./metadata.tpl"}
                        </div>
                
                        <div class="small-4 columns">
                                <fieldset class="buttons-form">
                                        {button label="Save" class='validate btn main_action btn_margin'}
                                </fieldset>
                        </div>

                </div>
        </div>
</form>