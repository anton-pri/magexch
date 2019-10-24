{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_single_tab' assign='magexch_product_single_tab'}
{tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_tab_color' assign='magexch_product_tab_color'}

{if $magexch_product_tab_color ne ''}
<script type="text/javascript">
var magexch_product_tab_color = '{$magexch_product_tab_color}';
<!--
{literal}
    $(document).ready(function(){
        $("<style type='text/css'> .magexch_product_tab_color{ background-color:"+magexch_product_tab_color+"!important;} </style>").appendTo("head");
        $('body').bind('switch_to_tab', custom_color_products_tab);
    });

    function custom_color_products_tab(event, tab, contents, tab_name) {
        $('#'+tab).addClass('magexch_product_tab_color');
        $('#contentscell').addClass('magexch_product_tab_color'); 
    }  
{/literal}
-->
</script>
{/if}

{if $magexch_product_single_tab eq 'Y'}

{include file="customer/products/custom_product_tabs_def_single_tab.tpl"}

{else}
{assign var='current_tab' value=0}

{foreach from=$sellers_data item=mag_seller}{if !$mag_seller.is_digital && $mag_seller.quantity>0}{assign var='current_tab' value=1}{/if}{/foreach}

{if !$current_tab}
    {foreach from=$sellers_data item=mag_seller}{if $mag_seller.is_digital}{assign var='current_tab' value=2}{/if}{/foreach}
{/if}

{if !$current_tab}
    {if $external_links}{foreach from=$external_links item=link}{assign var='current_tab' value=2}{/foreach}{/if}
{/if}

{if !$current_tab}{assign var='current_tab' value=1}{/if}

{include file="customer/products/custom_product_tabs_def.tpl"}

{/if}
