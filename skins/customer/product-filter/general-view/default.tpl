<script type="text/javascript">
var pf_is_use_ajax = '{$config.product.pf_is_ajax}';
var pf_range_values = new Array();
var pf_range_data = new Array();
var pf_range_ids = new Array();
var navigation_script = '{$navigation.script_raw|default:$navigation.script}';
$(function() {ldelim}
    cw_pf_onload();
{rdelim});
</script>
{capture name="menu"}
{if $config.product.pf_is_substring eq 'Y'}
    <div class="pf-title" id="substring_title">{$lng.lbl_substring}</div>
    <div class="pf-options" id="substring_box">
        <input type="text" id="att_substring" name="att[substring]" size="15" onchange="cw_pf_add_substring('{$navigation.script}')" value="{$search_prefilled.attributes.substring}" />
        {include file='buttons/button.tpl' button_title=$lng.lbl_go href="javascript: cw_pf_add_substring('`$navigation.script`')"}
        <div class="adv_search_link"><a href="index.php?target=search">{$lng.lbl_advanced_search}&nbsp;&raquo;</a></div>

    </div>

{literal}
<script type="text/javascript">
    $('#substring_title').click(function(){
                   $('#substring_box').slideToggle(250);
                   $(this).toggleClass('hidden');
                   return false;
     });
</script>
{/literal}
{/if}

{if $product_filter}
{foreach from=$product_filter item=pf name=pf}
{if $pf.is_selected}{assign var='pf_selected' value=true}{/if}
{if $pf.active && $pf.values} {* kornev, it's required for some of the special attributes *}
    <div class="pf-title" id="{$pf.field}_title">{$pf.name}
    </div>
    <div class="pf-options {if $pf.field eq "product_options_size"}size{/if}"  id="{$pf.field}_box">

        {if $pf.pf_display_type eq 'S'}
{* kornev, it's posible to add delete buttons here - commented for now - the selected items are removed in cw_product_search *}
            {*if $pf.selected.min || $pf.selected.max}
                <a href="#" onclick="cw_pf_load('{price_filter_url ns=$navigation.script att_id=$pf.attribute_id value_selected=$pf.selected}')" class="delete-filter">
                    {if $pf.is_price}
                    {include file='common/currency.tpl' value=$pf.selected.min_name} - {include file='common/currency.tpl' value=$pf.selected.max_name}
                    {else}
                    {$pf.selected.min_name} - {$pf.selected.max_name}
                    {/if}
                    {$lng.lbl_delete_filter}
                </a>
            {/if*}
            {if  $pf.max ge $pf.min}
            <input id="att_min_{$pf.attribute_id}" name="att[{$pf.attribute_id}][min]" type="hidden" value="{$pf.min}" />
            <input id="att_max_{$pf.attribute_id}" name="att[{$pf.attribute_id}][max]" type="hidden" value="{$pf.max}" />

            {if $pf.pf_display_type eq 'S' && ($pf.selected.min || $pf.selected.max)}
                <a href="javascript: void(0);" onclick="cw_pf_load('{price_filter_url ns=$navigation.script att_id=$pf.attribute_id value_selected=$pf.selected is_selected=1}')" class="delete-filter slider"></a>
            {/if}

            <div id="att_val_{$pf.attribute_id}" class="pf-values">
            {if $pf.selected.min || $pf.selected.max}
                {if $pf.is_price}
                    <span id='att_val_{$pf.attribute_id}_min' style="float: left">{include file='common/currency.tpl' value=$pf.selected.min_name}</span>  <span id='att_val_{$pf.attribute_id}_max' style="float: right">{include file='common/currency.tpl' value=$pf.selected.max_name}</span>
                {else}
                    <span id='att_val_{$pf.attribute_id}_min' style="float: left">{$pf.selected.min_name}</span>  <span id='att_val_{$pf.attribute_id}_max' style="float: right">{$pf.selected.max_name}</span>
                {/if}
            {else}
                {if $pf.is_price}
                    <span id='att_val_{$pf.attribute_id}_min' style="float: left">{include file='common/currency.tpl' value=$pf.min_name}</span>  <span id='att_val_{$pf.attribute_id}_max' style="float: right">{include file='common/currency.tpl' value=$pf.max_name}</span> 
                {else}
                    <span id='att_val_{$pf.attribute_id}_min' style="float: left">{$pf.min_name}</span>  <span id='att_val_{$pf.attribute_id}_max' style="float: right">{$pf.max_name}</span> 
                {/if}
            {/if}
            </div>


            <div id="pf_slider_{$pf.attribute_id}" class="pf-slider"></div>
            <script type="text/javascript">
                pf_range_values['{$pf.attribute_id}'] = [{foreach from=$pf.values item=val name='sld'}'{if $pf.is_price}{include file='common/currency.tpl' value=$val}{else}{$val}{/if}'{if !$smarty.foreach.sld.last},{/if}{/foreach}];
                {assign var='min_id' value=0}
                {assign var='counter' value=0}
                {assign var='max_id' value=$pf.values_counter-1}
                pf_range_ids['{$pf.attribute_id}'] = [{strip}
                    {foreach from=$pf.values item=val key=val_id name='sld'}'{$val_id}'{if !$smarty.foreach.sld.last},{/if}
                        {if $val_id eq $pf.selected.min}{assign var='min_id' value=$counter}{/if}
                        {if $val_id eq $pf.selected.max}{assign var='max_id' value=$counter}{/if}
                        {assign var='counter' value=$counter+1}
                    {/foreach}
                {/strip}];
                pf_range_data['{$pf.attribute_id}'] = [{$pf.values_counter-1}, {$min_id}, {$max_id}];
            </script>

{*
            <a href="#" onclick="cw_pf_add_filter('{$navigation.script}', '{$pf.attribute_id}')">{$lng.lbl_filter}</a>
*}
            {/if}
        {elseif $pf.pf_display_type eq 'R'}
            {if $pf.selected.min}
                <a href="javascript: void(0);" onclick="cw_pf_load('{price_filter_url ns=$navigation.script att_id=$pf.attribute_id value_selected=$pf.selected is_selected=1}')" class="delete_filter">
                    {$pf.selected.min} - {$pf.selected.max} {*$lng.lbl_delete_filter*}
                </a>
            {/if}
            {if $pf.max gt $pf.min}
            <table border=0 cellpadding="0" cellspacing="1">
            <tr>
                <td><input id="att_min_{$pf.attribute_id}" name="att[{$pf.attribute_id}][min]" type="text" value="{$pf.min}" size="5" /></td>
                <td>-</td>
                <td><input id="att_max_{$pf.attribute_id}" name="att[{$pf.attribute_id}][max]" type="text" value="{$pf.max}" size="5" /></td>
                <td><a href="#" onclick="cw_pf_add_filter('{$navigation.script}', '{$pf.attribute_id}')"><img src='{$ImagesDir}/rarrow.gif' alt='filter' /></a></td>
            </tr>
            </table>
            {/if}
        {elseif $pf.pf_display_type eq 'W'}
            {foreach from=$pf.values item=val}
                {assign var=is_selected value=0}
                {if $pf.is_selected && in_array($val.id, $pf.selected)}{assign var=is_selected value=1}{/if}
                  
                {capture name='onclick'}{price_filter_url ns=$navigation.script att_id=$pf.attribute_id value_selected=$pf.selected value_id=$val.id is_selected=$is_selected link=$val.link}{/capture}
                {if $val.image}
					<a href="{$smarty.capture.onclick}"{if $is_selected} class="delete-filter"{/if}>
                        <img src="{$val.image.tmbn_url}" alt="" width="20" /> 
                    	<span class="count">({$val.counter})</span>
					</a>
					<div class="clear"></div>
                {/if}
            {/foreach}

        {elseif $pf.pf_display_type eq 'G'}
              {foreach from=$pf.values item=val}
                {assign var=is_selected value=0}
                {if $pf.is_selected && in_array($val.id, $pf.selected)}{assign var=is_selected value=1}{/if}
                  
                {capture name='onclick'}{price_filter_url ns=$navigation.script att_id=$pf.attribute_id value_selected=$pf.selected value_id=$val.id is_selected=$is_selected link=$val.link}{/capture}
                {if $val.image}
					<div class='pf-display-type-G {if $is_selected}selected{/if}'>
					{if $is_selected}<div class='select-flag'></div>{/if}
					<a href="{$smarty.capture.onclick}"{if $is_selected} class="delete-filter"{/if}>	
						<img src="{$val.image.tmbn_url}" title='{$val.name} ({$val.counter})' alt="{$val.name}" />
					</a>
					</div>
                {/if}
			  {/foreach}
			  <div class="clear"></div>

        {elseif $pf.pf_display_type eq 'E'}
            {foreach from=$pf.values item=val}
                {assign var=is_selected value=0}
                {if $pf.is_selected && in_array($val.id, $pf.selected)}{assign var=is_selected value=1}{/if}

                {capture name='onclick'}{price_filter_url ns=$navigation.script link=$val.link att_id=$pf.attribute_id value_selected=$pf.selected value_id=$val.id is_selected=$is_selected}{/capture}
				<a href="{$smarty.capture.onclick}"{if $is_selected} class="delete-filter"{/if}>
                    {if $val.image}
                        <img src="{$val.image.tmbn_url}" alt="" width="20" />
                    {/if} 
					{$val.name} <span class="count">({$val.counter})</span>
				</a>
                {if $val.image}<div class="clear"></div>{/if}
            {/foreach}

        {elseif $pf.pf_display_type eq 'T' || $pf.pf_display_type eq 'P' }
            {foreach from=$pf.values item=val}
                {assign var=is_selected value=0}
                {if $pf.is_selected && in_array($val.id, $pf.selected)}{assign var=is_selected value=1}{/if}

                <a href="{price_filter_url ns=$navigation.script att_id=$pf.attribute_id link=$val.link value_selected=$pf.selected value_id=$val.id is_selected=$is_selected}"{if $is_selected} class="delete-filter"{/if}>
                    {if $pf.type eq 'yes_no'}{include file='main/select/yes_no.tpl' value=$val.id is_text=1}{else}{$val.name}{/if}
                    <span class="count">({$val.counter}{if $config.product.pf_show_from_price eq 'Y' && $val.min_price} {$lng.lbl_from|strtolower} {include file='common/currency.tpl' value=$val.min_price}{/if})</span>
                </a>
                
            {/foreach}

        {/if}
    </div>
{/if}
<script type="text/javascript">
    $('#{$pf.field}_title').click(function(){ldelim}
                   $('#{$pf.field}_box').slideToggle(250);
                   $(this).toggleClass('hidden');
                   return false;
     {rdelim});
</script>
{/foreach}

{/if}

{if $config.product.pf_is_substring eq 'Y' || $product_filter}
<div class="clear"></div>
{/if}
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_product_filter content=$smarty.capture.menu style='product-filter' }
