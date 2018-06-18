{foreach from=$group['metadata'] key=i item=data}
    {include file="actions/components/form_input.tpl"
    divClass="small-4{if $i eq count($group['metadata']) - 1} end{/if}"
    title=$data["name"] name="metadata[`$group['id']`][`$data['id']`]"
    id="metadata[`$group['id']`][`$data['id']`]" type=$data['type'] id_node="" value="{$data['value']}"}
{/foreach}

