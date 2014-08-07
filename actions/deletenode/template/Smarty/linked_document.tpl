<div class="action_header">
    <h2>{t}You can't delete the node{/t}</h2>
    <fieldset class="buttons-form">
        {button class="close-button btn main_action" label="Close"}
    </fieldset>
</div>
<div class="message-warning message">
    <p>{t}This document has a symbolic link{/t}.</p>
</div>
<div class="action_content">
    <p>{t}To delete this node you have to break the link with the following node(s){/t}:</p>
    {foreach from=$path_symbolics item=path_symbolic}
        <p><strong>{$path_symbolic}</strong></p>
    {/foreach}
</div>

