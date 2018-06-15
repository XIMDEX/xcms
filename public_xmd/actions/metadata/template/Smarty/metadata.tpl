{section name=i loop=$metadata}
    {include file="actions/components/form_input.tpl" divClass="small-6 "
    title=$metadata[i]["name"] name="metadata[`$metadata[i]['id']`]" 
    id="metadata[`$metadata[i]['id']`]" id_node="" value="{$metadata[i]['value']}"}
{/section}