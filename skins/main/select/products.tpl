{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
# <input type="text" size="7" name="{$name}" id="{$name|id}" readonly="readonly" class='micro' />&nbsp;
<input type="text" size="32" name="{$name|id}_name" id="{$name|id}_name" readonly="readonly" />&nbsp;
{if $amount_name}
<input type="text" size="5" name="{$amount_name}" id="{$amount_name|id}" class='micro' />&nbsp;
{/if}
{if $without_form}
<input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript:popup_products('{$name|id}', '{$name|id}_name', '{if $amount_name}{$amount_name|id}{/if}', ['{$cat_id}', '{$supplier_id}']);" />
{else}
<input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript:popup_products('{$form}.{$name}', '{$form}.{$name}_name', '{if $amount_name}{$form}.{$amount_name}{/if}', ['{$cat_id}', '{$supplier_id}']);" />
{/if}
