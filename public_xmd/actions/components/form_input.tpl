<div class="{$divClass} columns">
        <div class="input">
                <label for="{$name}" class="label_title label_general">{t}{$title}{/t}</label>
                <input type="{$type|default:"text"}" name="{$name}" id="{$id}" value="{$value}"
                class="input_general cajaxg validable "
                            data-idnode="{$id_node}" />
        </div>
</div>