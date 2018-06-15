<div class="{$divClass} columns">
        <div class="input-select">
                <label for="{$name}"
                class="{if $required == true}required{/if} label_title label_general">{$title}</label>
                <select name="{$name}" id="{$id}" {if $required == true}required{/if}
                 class="dropdown full_size cajag validable not_empty">
                        <option value="">{t}{$optionTitle}{/t}</option>
                        {foreach from=$items item=item}
                        <option value="{$item['id']}">{$item['name']}</option>
                        {/foreach}
                </select>
        </div>
</div>