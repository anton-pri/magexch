{if $product_options}
<script type="text/javascript">
<!--
var alert_msg = '{$alert_msg}';
{literal}
(function($) {
    $.fn.extend( {
        limiter: function(limit, elem) {
            $(this).on("keyup focus", function() {
                setCount(this, elem);
            });
            function setCount(src, elem) {
                var chars = src.value.length;
                if (chars > limit) {
                    src.value = src.value.substr(0, limit);
                    chars = limit;
                }
                elem.html( limit - chars );
            }
            setCount($(this)[0], elem);
        }
    });
})(jQuery);
{/literal}
-->
</script>
{include file='addons/product_options/customer/products/check-options.tpl'}

{foreach from=$product_options item=v}
{if $v.options ne '' || $v.type eq 'T'}
<div class="product_field{cycle values=", cycle"}">
<label>{$v.name}</label>
{if $cname ne ""}
{assign var="poname" value="$cname[`$v.product_option_id`]"}
{else}
{assign var="poname" value="product_options[`$v.product_option_id`]"}
{/if}
{if $v.type eq 'T'}
  {if $v.text_type eq 'A'}
    <textarea id="po{$v.product_option_id}" name="{$poname}" {if $onchange} onchange="{$onchange}"{/if} rows="{$config.Product_Options.textarea_rows}" cols="{$config.Product_Options.textarea_cols}">{$v.default|escape}</textarea>
  {else}
    <input id="po{$v.product_option_id}" type="text" name="{$poname}" value="{$v.default|escape}"{if $onchange} onchange="{$onchange}"{/if} />
  {/if}
{if $v.text_limit gt 0}
<div>{$lng.lbl_chars_left}:&nbsp;<span id="max_chars_{$v.product_option_id}"></span></div>
<script type="text/javascript">
$(document).ready(function() {ldelim}
    var elem = $("#max_chars_{$v.product_option_id}");
    $("#po{$v.product_option_id}").limiter({$v.text_limit|default:100}, elem);
{rdelim});
</script>
{/if}

{else}
<select id="po{$v.product_option_id}" name="{$poname}"{if $disable} disabled="disabled"{/if}{if $onchange} onchange="{$onchange}"{else} onchange="check_options();"{/if}>
{foreach from=$v.options item=o}
<option value="{$o.option_id}"{if $o.selected eq 'Y'} selected="selected"{/if}>{$o.name}{if $v.type eq 'Y' && $o.price_modifier ne 0} ({if $o.modifier_type}{$o.price_modifier|formatprice}%{else}{include file='common/currency.tpl' value=$o.price_modifier display_sign=1 plain_text_message=1}{/if}){/if}</option>
{/foreach}
</select><div class="clear"></div>
{/if}
</div>
{/if}
{/foreach}

{if $products_options_ex ne ""}
<div id="exception_msg" color="red"></div>
    {if $err ne ''}
	<div class="CustomerMessage">{$lng.txt_product_options_combinations_warn}:</div>
        {foreach from=$products_options_ex item=v}
        	<div>{foreach from=$v item=o}{$o.option_name}: {$o.name}<br />{/foreach}</div>
        {/foreach}
    {/if}
{/if}

<script type="text/javascript">
var pid = '{$product.product_id}';
{literal}
$(document).ready(function() {
    if (window.localStorage) {
        var data = $.parseJSON(window.localStorage.getItem(pid));

        for (var i in data) {

            if (data[i]) {
                document.getElementById(i).value = data[i];
            }
        }
    }
    check_options();
});
{/literal}
</script>
{/if}
