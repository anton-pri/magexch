<div class="box" style="padding-top: 10px">

{include file='common/subheader.tpl' title=$lng.lbl_dod_select_products_from}

<ul class="dod-list">
<li class="dod-entities-block"> <label for='dod_prods_id_prod_0' class='error' style='display:none'>Select at least one product or category</label></li>
<li class="dod-entities-block" id="dod-discount-products-always-show-block">
            <table>
                <tr>
                    <th colspan="2">{$lng.lbl_dod_products}</th>
                </tr>
                {product_selector multiple=1 prefix_name='dod_bonus[D][products]' prefix_id='dod_prods' products=$dod_bonus.D.products}
            </table>
            <table>
                <tr>
                    <th>{$lng.lbl_dod_categories}</th>
                </tr>

                <tr>{assign var='_group' value='dod_cats'}
                    <td id="{$_group}_box_1">
                        <div id="{$_group}_show_category" style="display:none">
                            <div class="bd"><div class="category_ajax" id="{$_group}_body">{$lng.lbl_loading}</div></div>
                        </div>
                        <input id="{$_group}_catid_0" type="text" size="11" maxlength="11" name="dod_bonus[D][cats][0][id]" value="{$dod_bonus.D.cats[0].id}" class="micro" />
                        <input id="{$_group}_catname_0" type="text" size="40" maxlength="255" name="dod_bonus[D][cats][0][name]" value="{$dod_bonus.D.cats[0].name|escape}" readonly="readonly" />
                        <input id="{$_group}_catqty_0" type="hidden" size="11" maxlength="11" name="dod_bonus[D][cats][0][quantity]" value="{$dod_bonus.D.cats[0].quantity|escape}" />
                        <img src="{$ImagesDir}/calendar.jpg" width="22" onclick="dod_show_cats(this, '{$_group}');" id="{$_group}_link_0" />
                    </td>
                    <td id="{$_group}_add_button">{include file="main/multirow_add.tpl" mark=$_group is_lined=true}</td>
                </tr>

            </table>

    {if $dod_bonus.D.cats ne '' && $dod_bonus.D.cats|@count gt 1}
    {assign var='_group' value='dod_cats'}
    <script type="text/javascript">
        {foreach from=$dod_bonus.D.cats item=_elem name=$_group}
        {if !$smarty.foreach.$_group.first}
        add_inputset_preset('{$_group}', document.getElementById('{$_group}_add_button'), false,
        [
            {ldelim}regExp: /{$_group}_catid/, value: '{$_elem.id|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catname/, value: '{$_elem.name|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_catqty/, value: '{$_elem.quantity|escape}'{rdelim},
            {ldelim}regExp: /{$_group}_link/, value: '0'{rdelim},
        ]
        );
        {/if}
        {/foreach}
    </script>
    {/if}

        {if $dod_mans && $dod_mans|@count gt 0}
        <table>
            <tr>
                <th colspan="2" align='left'>{$lng.lbl_dod_manufacturers}</th>
            </tr>
            <tr>
                <td id="dod_mans_box_1">
                    <select id="dod_mans_id" name="dod_bonus[D][mans][]" multiple="multiple" size="10">
                       {foreach from=$dod_mans item='manufacturer'}
                          <option value="{$manufacturer.manufacturer_id}" 
{foreach from=$dod_bonus.D.mans item=man}{if $man.id eq $manufacturer.manufacturer_id}selected="selected"{/if}{/foreach}
                          >{$manufacturer.manufacturer}</option>
                       {/foreach}
                    </select>
                </td>
            </tr>
        </table>
        {/if}

        <table>
            <tr>
                <th align='left'>{$lng.lbl_attributes}</th>
            </tr>
            <tr>
            <td>
                <div id='dod_attributes'>
                <select id='new_attribute_bonus' size="1">
                    {foreach from=$dod_attr item='attributes'}
                    <option value="{$attributes.attribute_id}">{$attributes.name}</option>
                    {/foreach}
                </select>
                <a onclick="javascript: ajaxGet('index.php?target=deal_of_day&action=attributes&attribute_id='+$('#new_attribute_bonus').val());" href="javascript: void(0);">
                <img src="{$ImagesDir}/admin/plus.png" align='top' alt='+' />
                </a>
                <script type="text/javascript">
                    $i = 0;
                    {foreach from=$dod_bonus.D.attr item=_elem}
                     setTimeout("ajaxGet('index.php?target=deal_of_day&action=attributes&bd_id={$_elem.bd_id}')",100*$i);$i++;
                    {/foreach}
                </script>
                </div>
                <sup>* {$lng.txt_dod_alphabetical_order}</sup>
            </td>
            </tr>
        </table>



</li>
</ul>

{include file='common/subheader.tpl' title=$lng.lbl_dod_details}

<div class="input_field_0">
    <label class='multilan required'>
        {$lng.lbl_dod_generator_date}
        
    </label>
    {include file='main/select/date.tpl' name='generator_data[startdate]' value=$generator_data.startdate class='required'} -
    {include file='main/select/date.tpl' name='generator_data[enddate]' value=$generator_data.enddate class='required'}
</div>

<div class="input_field_1">
    <label class='multilan required'>
        {$lng.lbl_dod_generator_title} 
    </label>
    <input type="text" size="50" maxlength="255" name="generator_data[title]" value="{$generator_data.title|default:$lng.lbl_dod_unknown|escape}"{if $read_only} disabled{/if} class='required' />
</div>

<div class="input_field_1">
    <label class='multilan required'>
        {$lng.lbl_dod_generator_desc} 
    </label>
    {include file='main/textarea.tpl' name="generator_data[description]" data="`$generator_data.description`" init_mode='exact' class='required'}
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_dod_generator_active}
    </label>
    <input type="checkbox" name="generator_data[active]" value="1"{if $generator_data.active eq 1} checked{/if} />
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_dod_generator_position}
    </label>
    <input type="text" size="6" maxlength="11" name="generator_data[position]" class='micro integer' value="{$generator_data.position|default:0|escape}"{if $read_only} disabled{/if} />
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_dod_generator_interval}
    </label>
    <input type="text" size="6" maxlength="11" name="generator_data[dod_interval]" class='micro integer' value="{$generator_data.dod_interval|default:0|escape}"{if $read_only} disabled{/if} />
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_dod_generator_interval_type|default:'Interval type'}
    </label>
    <select name="generator_data[dod_interval_type]" {if $read_only} disabled{/if}> 
    <option value="D" {if $generator_data.dod_interval_type eq "D"}selected="selected"{/if}>{$lng.lbl_dod_days_to_pass_between_offer_is_generated|default:'days to pass between dod offer is generated'}</option>
    <option value="T" {if $generator_data.dod_interval_type eq "T"}selected="selected"{/if}>{$lng.lbl_dod_times_within_specified_period_dod_offer_will_be_generated|default:'times within specified period dod offer will be generated'}</option>
    </select>
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_dod_no_item_repeat}
    </label>
    <input type="checkbox" name="generator_data[no_item_repeat]" value="1"{if $generator_data.no_item_repeat eq 1} checked{/if} />
</div>

</div>
