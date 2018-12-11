{if $included_tab eq 'add_search'}
<div class="box" id="manufacturer">

<div class="input_field_0">
    <label>{$lng.lbl_manufacturers}</label>
    {include file='addons/manufacturers/select/manufacturer.tpl' name='posted_data[attribute_names][manufacturer_id][]' value=$search_prefilled.attribute_names.manufacturer_id multiple=5}
</div>

</div>
{/if}
