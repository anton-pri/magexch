{if $product_options && $product_id}
  {if $product_list_name eq ""}{assign var="product_list_name" value="po"}{/if}
  {if $nojs ne "Y"}
    {include file="addons/accessories/check_options_multiple_products.tpl"}
  {/if}

  {foreach from=$product_options item="options"}
      {if $options.options ne "" || $options.is_modifier eq "T"}
        <div class="product_field{cycle values=", cycle"}">
            <label>{if $usertype eq "A"}{$options.class}{else}{$options.name|default:$options.class}{/if}</label>
          
            {if $input_field_name ne ""}
              {assign var="poname" value="$input_field_name[`$product_id`][`$options.class_id`]"}
            {else}
              {assign var="poname" value="product_options[`$product_id`][`$options.class_id`]"}
            {/if}
            {if $options.is_modifier eq "T"}
              <input id="{$product_list_name}_{$product_id}_{$options.class_id}" type="text" name="{$poname}" value="{$options.default|escape}"{if $nojs ne "Y"}{if $onchange} onChange="{$onchange}"{/if}{/if} />
            {else}
              <select id="{$product_list_name}_{$product_id}_{$options.class_id}" name="{$poname}"{if $disable} disabled="disabled"{/if}{if $nojs ne "Y"} onChange="javascript: accessoriesCheckOptions('{$product_list_name}', {$product_id});"{/if}{if $onchange} onChange="{$onchange}"{/if}>
                {foreach from=$options.options item="option"}
                  <option value="{$option.option_id}"{if $option.selected eq "Y"} selected="selected"{/if}>{$option.name}{if $options.is_modifier eq "Y" && $option.price_modifier ne 0} ({if $option.modifier_type ne "%"}{include file="common/currency.tpl" value=$option.price_modifier display_sign=true plain_text_message=true}{else}{$option.price_modifier}%{/if}){/if}</option>
                {/foreach}
              </select>
            {/if}
         </div>
      {/if}
    {/foreach}
    {if $product_options_ex}
     <div class="product_field{cycle values=", cycle"}">
       <span id="{$product_list_name}_product_options_exception_message_{$product_id}" style="color: red;"></span>
      {if $exception_errors.$product_id}
        <p><font class="CustomerMessage">{$lng.txt_product_options_combinations_warn}:</font></p>
        {foreach from=$product_options_ex item="options_exceptions"}
            <p>
                {foreach from=$options_exceptions item="option"}{if $usertype eq "A"}{$option.class}{else}{$option.name}{/if}: {$option.name}<br />{/foreach}
            </p>
        {/foreach}
      {/if}
      </div>
    {/if}

{/if}
