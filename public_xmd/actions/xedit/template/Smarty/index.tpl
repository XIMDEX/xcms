<div class="html5editor">
    {if ($enabled)}
        {if ($url)}
            <iframe src="{$url}?url={$ximdex_API}&_action=/xedit/{$id}/get&id={$id}&type={$type}&token[field]=token&token[value]={$token}"
                id="iframe-html5editor"></iframe>
        {else}
            <h2 class="warning">{t}There is not a URL for the HTML editor in the system configuration{/t}</h2>
        {/if}
    {else}
        <h2 class="notice">{t}The HTML editor is not enabled in the system configuration{/t}</h2>
    {/if}
</div>