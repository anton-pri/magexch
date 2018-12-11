{if $product.options}
    <div style="padding-top: 40px">&nbsp;</div>
    <script type="text/javascript">
        <!--
        var alert_msg = 'N';
        var productid = '{$product.product_id}';
        -->
    </script>
    {include file='addons/product_options/customer/products/check-options-list.tpl' productid=$product.product_id}

    {foreach from=$product.options item=v}
        {if $v.options ne '' || $v.type eq 'T'}
            <div class="product_field{cycle values=", cycle"}">
                <label {if $v.hidden}style="display:none"{/if}>{$v.name}</label>
                {if $cname ne ""}
                    {assign var="poname" value="$cname[`$v.product_option_id`]"}
                {else}
                    {assign var="poname" value="product_options[`$v.product_option_id`]"}
                {/if}
                {if $v.hidden}
                    {foreach from=$v.options item=o}
                        <input id="po{$product.product_id}_{$v.product_option_id}" type="hidden" name="{$poname}" value="{$o.option_id}" />
                    {/foreach}
                {elseif $v.type eq 'T'}
                    <input id="po{$product.product_id}_{$v.product_option_id}" type="text" name="{$poname}" value="{$v.default|escape}"{if $onchange} onchange="{$onchange}"{/if} />
                {else}
                    <select id="po{$product.product_id}_{$v.product_option_id}" name="{$poname}"{if $disable} disabled="disabled"{/if}{if $onchange} onchange="{$onchange}"{else} onchange="check_options('{$product.product_id}');"{/if}>
                    {foreach from=$v.options item=o}
                        <option value="{$o.option_id}"{if $o.selected eq 'Y'} selected="selected"{/if}>{$o.name}{if $v.type eq 'Y' && $o.price_modifier ne 0} ({if $o.modifier_type}{$o.price_modifier|formatprice}%{else}{include file='common/currency.tpl' value=$o.price_modifier display_sign=1 plain_text_message=1}{/if}){/if}</option>
                    {/foreach}
                    </select><div class="clear"></div>
                {/if}
            </div>
        {/if}
    {/foreach}

    {if $product.options_ex ne ""}
        <div id="exception_msg_$product.product_id" color="red"></div>
        {if $err ne ''}
            <div class="CustomerMessage">{$lng.txt_product_options_combinations_warn}:</div>
            {foreach from=$product.options_ex item=v}
                <div>{foreach from=$v item=o}{$o.option_name}: {$o.name}<br />{/foreach}</div>
            {/foreach}
        {/if}
    {/if}

    <script type="text/javascript">
    {literal}
        $(document).ready(function() {
            if (window.localStorage) {
    {/literal}
                var pid = '{$product.product_id}';
                store_options[pid] = [];
                {foreach from=$product.options item=v}
                    {if $v.options ne '' || $v.type eq 'T'}
                        var product_option_id = {$v.product_option_id};
                        store_options[pid].push(product_option_id);
                    {/if}
                {/foreach}
    {literal}
                var localData = {};
                for (var o in store_options[pid]) {
                    localData['po' + store_options[pid][o]] = document.getElementById('po' + pid + '_' + store_options[pid][o]).value;
                }
                var data = JSON.stringify(localData);
                window.localStorage.setItem(pid, data);
            }
        });
    {/literal}
    </script>
{/if}