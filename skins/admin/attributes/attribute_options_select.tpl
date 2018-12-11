    <label>{$lng.lbl_att_values}</label>
    <table class="table table-striped" width="100%">
    <thead>
    <tr>
        <th class='multilan'>{$lng.lbl_value}</th>
        {if !$ranges_for_scalar}
        <th>{$lng.lbl_default}</th>
        {/if}
        {if $ranges_for_scalar}
            <th>{$lng.lbl_from}</th>
            <th>{$lng.lbl_to}</th>
        {/if}
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
    </thead>
    <tr>
        <td id="dsl_box_0">
            <span style="cursor: pointer;" onclick="show_hide_description_preset(this, 'dsl');" title="{$lng.lbl_show_hide_hidden_fields}">&#9658;</span>
            <input type="hidden" name="posted_data[default_values_select_id][0]" id="default_values_select_id_0" value="" />
            <input type="text" class="form-control inline-control" name="posted_data[default_values_select][0]" id="default_values_select_0" value="" />
            <input type="hidden" name="posted_data[default_values_select_description][0]" id="default_values_select_description_0" value="" />
        </td>
        {if !$ranges_for_scalar}
        <td id="dsl_box_1" align="center"><input type="radio" name="posted_data[default_values_select_is_default]" id="default_values_select_is_default_0" value="0" /></td>
        {/if}

        {if $ranges_for_scalar}
            <td id="dsl_box_8"> <input type="text" class="form-control" name="posted_data[default_values_select_from][0]" id="default_values_select_from_0" size="7" value="" /></td>
            <td id="dsl_box_9"> <input type="text" class="form-control" name="posted_data[default_values_select_to][0]" id="default_values_select_to_0" size ="7" value="" /></td>
        {/if}
        <td id="dsl_box_2" class="narrow_input"><input type="text" class="form-control" name="posted_data[default_values_select_orderby][0]" id="default_values_select_orderby_0" value="0" size="3" /></td>
        <td id="dsl_box_3" align="center"><input type="checkbox" name="posted_data[default_values_select_active][0]" id="default_values_select_active_0" value="1" checked /></td>
        <td id="dsl_box_4" align="center"><input type="checkbox" name="posted_data[default_values_select_facet][0]" id="default_values_select_facet_0" value="1" /></td>
        <td id="dsl_box_5">{include file='main/select/attribute_image.tpl' name="posted_data[default_select_images][0]" id="default_images_select_0" value="0" is_please_select=1}</td>
        {if $attribute.pf_is_use}
        <td id="dsl_box_6">{include file='main/select/attribute_image.tpl' name="posted_data[default_select_pf_images][0]" id="default_pf_images_select_0" value="0" is_please_select=1}</td>
        {/if}
        {include file='admin/attributes/attribute_value_td_field.tpl' id="dsl_box_7" size="7" ident='default_values_select' key=0}
        <td id="dsl_add_button">{include file='main/multirow_add.tpl' mark='dsl'}</td>
    </tr>
    </table>
    {if $attribute.default_values}
    <script type="text/javascript">
{* kornev, checked element have to be last one - in other case the checked status will be cloned *}
    {foreach from=$attribute.default_values key=key item=elm}
        add_inputset_preset('dsl', document.getElementById('dsl_add_button'), false, [
            {if $elm.is_default}{assign var='checked_element' value=$key+1}{/if}
            {ldelim}regExp: /default_values_select_{$key+1}/, value: '{$elm.value|escape:javascript}'{rdelim},
            {ldelim}regExp: /default_values_select_orderby_{$key+1}/, value: '{$elm.orderby|escape}'{rdelim},
            {ldelim}regExp: /default_values_select_active_{$key+1}/, value: '{$elm.active|escape}'{rdelim},
            {ldelim}regExp: /default_values_select_facet_{$key+1}/, value: {if $elm.facet}true{else}false{/if}{rdelim},
            {ldelim}regExp: /default_values_select_description_{$key+1}/, value: '{$elm.description|escape}'{rdelim},
            {ldelim}regExp: /default_values_select_id_{$key+1}/, value: '{$elm.attribute_value_id|escape}'{rdelim},
            {ldelim}regExp: /default_images_select_{$key+1}/, value: '{$elm.image_id|escape}'{rdelim}
            {if $attribute.pf_is_use}
            ,{ldelim}regExp: /default_pf_images_select_{$key+1}/, value: '{$elm.pf_image_id|escape}'{rdelim}
            {/if}
			{if $ranges_for_scalar}
               ,{ldelim}regExp: /default_values_select_from_{$key+1}/, value: "{$elm.value_key}".split('-')[0]{rdelim}
               ,{ldelim}regExp: /default_values_select_to_{$key+1}/, value: "{$elm.value_key}".split('-')[1]{rdelim}
            {/if}
            {include file='admin/attributes/attribute_preset_item.tpl' ident='default_values_select'}
        ]);
    {/foreach}
    {if $checked_element}
        {if !$ranges_for_scalar}
			document.getElementById('default_values_select_is_default_{$checked_element}').checked = true;
        {else}
            $('#dsl_box_0_{$checked_element-1}').parent().hide();
			document.write('<input type="hidden" name="posted_data[default_values_select_is_default]" value="{$checked_element}" />');
        {/if}
    {/if}
    </script>
    {/if}

