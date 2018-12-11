    <label>{$lng.lbl_att_values}</label>
    <table class="header">
    <tr>
        <th class='multilan'>{$lng.lbl_value}</th>
        <th>{$lng.lbl_default}</th>
        <th>{$lng.lbl_orderby}</th>
        <th>{$lng.lbl_active}</th>
        <th>{$lng.lbl_facet}</th>
        <th>{$lng.lbl_image}</th>
        {if $attribute.pf_is_use}
        <th>{$lng.lbl_product_filter_image}</th>
        {/if}
        {include file='admin/attributes/attribute_value_th_title.tpl'}
        <th>&nbsp;</th>
    </tr>
    <tr>
    <tr>
        <td id="dmsl_box_0">
            <span style="cursor: pointer;" onclick="show_hide_description_preset(this, 'dmsl');" title="{$lng.lbl_show_hide_hidden_fields}">&#9658;</span>
            <input type="hidden" name="posted_data[default_values_multiselect_id][0]" id="default_values_multiselect_id_0" value="" />
            <input type="text" name="posted_data[default_values_multiselect][0]" id="default_values_multiselect_0" value="" />
            <input type="hidden" name="posted_data[default_values_multiselect_description][0]" id="default_values_multiselect_description_0" value="" />
        </td>
        <td id="dmsl_box_1"><input type="checkbox" name="posted_data[default_values_multiselect_is_default][0]" id="default_values_multiselect_is_default_0" value="1" /></td>
        <td id="dmsl_box_2"><input type="text" name="posted_data[default_values_multiselect_orderby][0]" id="default_values_multiselect_orderby_0" value="0" size="3" /></td>
        <td id="dmsl_box_3"><input type="checkbox" name="posted_data[default_values_multiselect_active][0]" id="default_values_multiselect_active_0" value="1" /></td>
        <td id="dmsl_box_4"><input type="checkbox" name="posted_data[default_values_multiselect_facet][0]" id="default_values_multiselect_facet_0" value="1" /></td>
        <td id="dmsl_box_5">{include file='main/select/attribute_image.tpl' name="posted_data[default_multiselect_images][0]" id="default_images_multiselect_0" value="0" is_please_select=1}</td>
        {if $attribute.pf_is_use}
        <td id="dmsl_box_6">{include file='main/select/attribute_image.tpl' name="posted_data[default_multiselect_pf_images][0]" id="default_pf_images_multiselect_0" value="0" is_please_select=1}</td>
        {/if}
        {include file='admin/attributes/attribute_value_td_field.tpl' id="dmsl_box_7" size="7" ident='default_values_multiselect' key=0}
        <td id="dmsl_add_button">{include file='main/multirow_add.tpl' mark='dmsl'}</td>
    </tr>
    </table>
    {if $attribute.default_value}
    <script type="text/javascript">
    {foreach from=$attribute.default_value key=key item=elm}
        add_inputset_preset('dmsl', document.getElementById('dmsl_add_button'), false, [
            {ldelim}regExp: /default_values_multiselect_is_default_{$key+1}/, value: {if $elm.is_default}true{else}false{/if} {rdelim},
            {ldelim}regExp: /default_values_multiselect_{$key+1}/, value: '{$elm.value|escape}'{rdelim},
            {ldelim}regExp: /default_values_multiselect_orderby_{$key+1}/, value: '{$elm.orderby|escape}'{rdelim},
            {ldelim}regExp: /default_values_multiselect_active_{$key+1}/, value: {if $elm.active}true{else}false{/if}{rdelim},
            {ldelim}regExp: /default_values_multiselect_facet_{$key+1}/, value: {if $elm.facet}true{else}false{/if}{rdelim},
            {ldelim}regExp: /default_values_multiselect_description_{$key+1}/, value: '{$elm.description|escape}'{rdelim},
            {ldelim}regExp: /default_values_multiselect_id_{$key+1}/, value: '{$elm.attribute_value_id|escape}'{rdelim},
            {ldelim}regExp: /default_images_multiselect_{$key+1}/, value: '{$elm.image_id|escape}'{rdelim}
            {if $attribute.pf_is_use}
            ,{ldelim}regExp: /default_pf_images_multiselect_{$key+1}/, value: '{$elm.pf_image_id|escape}'{rdelim}
            {/if}
            {include file='admin/attributes/attribute_preset_item.tpl' ident='default_values_multiselect'}
        ]);
    {/foreach}
    </script>
    {/if}

