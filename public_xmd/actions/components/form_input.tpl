<div class="{$divClass} columns">
    <div class="input">
        <label for="{$name}"
               class="label_title {if isset($required) && $required == true}required {/if}label_general">{t}{$title}{/t}</label>
        <input type="{$type|default:"text"}" name="{$name}"
               id="{$id}" value="{$value}"
               class="input_general cajaxg {if isset($required) && $required == true}validable not_empty {/if}"
               data-idnode="{$id_node}"/>
    </div>
</div>