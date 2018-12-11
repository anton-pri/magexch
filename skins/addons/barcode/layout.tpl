{if !$is_pdf || ($elements.label_barcode_ean.display ne 'none' && $is_pdf)}
<span id="label_barcode_ean">{$lng.lbl_eancode}</span>
{/if}
{if !$is_pdf || ($elements.barcode_ean.display ne 'none' && $is_pdf)}
<span id="barcode_ean">{$product.eancode}</span>
{/if}
{if !$is_pdf || ($elements.barcode_bar.display ne 'none' && $is_pdf)}
{include file='main/label/barcode.tpl' barcode=$product.eancode type=$config.barcode.gen_product_code width=2 height=$layout.data.barcode_height*500/210 id='barcode_bar' width=$layout.data.barcode_width}
{/if}
{if !$is_pdf || ($elements.label_barcode_price.display ne 'none' && $is_pdf)}
<span id="label_barcode_price">{$lng.lbl_price}</span>
{/if}
{if !$is_pdf || ($elements.barcode_price.display ne 'none' && $is_pdf)}
<span id="barcode_price">{include file='common/currency.tpl' value=$product.display_price|default:0}</span>
{/if}
{if !$is_pdf || ($elements.label_barcode_price_discounted.display ne 'none' && $is_pdf)}
<span id="label_barcode_price_discounted">{$lng.lbl_discounted_price}</span>
{/if}
{if !$is_pdf || ($elements.barcode_price_discounted.display ne 'none' && $is_pdf)}
<span id="barcode_price_discounted">{include file='common/currency.tpl' value=$product.display_discounted_price|default:0}</span>
{/if}
{if !$is_pdf || ($elements.label_barcode_supplier_sku.display ne 'none' && $is_pdf)}
<span id="label_barcode_supplier_sku">{$lng.lbl_supplier_productcode}</span>
{/if}
{if !$is_pdf || ($elements.barcode_supplier_sku.display ne 'none' && $is_pdf)}
<span id="barcode_supplier_sku">{$product.supplier_code}</span>
{/if}
{if !$is_pdf || ($elements.label_barcode_discount.display ne 'none' && $is_pdf)}
<span id="label_barcode_discount">{$lng.lbl_discount}</span>
{/if}
{if !$is_pdf || ($elements.barcode_discount.display ne 'none' && $is_pdf)}
<span id="barcode_discount">{assign var='discount' value=0}{if $product.display_price and $product.display_price}{math equation='a-b' a=$product.display_price b=$product.display_discounted_price assign='discount'}{/if}{if $discount < 0}{assign var='discount' value=0}{/if}{include file='common/currency.tpl' value=$discount|default:0}</span>
{/if}
{if !$is_pdf || ($elements.barcode_supplier_sku_bar.display ne 'none' && $is_pdf)}
{include file='main/label/barcode.tpl' barcode=$product.supplier_code type=$config.barcode.gen_product_code id='barcode_supplier_sku_bar' width=2 height=$layout.data.barcode_height_supplier*500/210 width=$layout.data.barcode_width_supplier}
{/if}
{if !$is_pdf || ($elements.label_barcode_productcode.display ne 'none' && $is_pdf)}
<span id="label_barcode_productcode">{$lng.lbl_sku}</span>
{/if}
{if !$is_pdf || ($elements.barcode_productcode.display ne 'none' && $is_pdf)}
<span id="barcode_productcode">{$product.productcode}</span>
{/if}
{if $addons.sn}
    {if !$is_pdf || ($elements.label_barcode_sn.display ne 'none' && $is_pdf)}
<span id="label_barcode_sn">{$lng.lbl_serial_number}</span>
    {/if}
    {if !$is_pdf || ($elements.barcode_sn.display ne 'none' && $is_pdf)}
<span id="barcode_sn">{$product.sn}</span>
    {/if}
    {if !$is_pdf || ($elements.barcode_sn_bar.display ne 'none' && $is_pdf)}
{include file='main/label/barcode.tpl' barcode=$product.sn type=$config.barcode.gen_product_code id='barcode_sn_bar' width=2 height=$layout.data.barcode_height_sn*500/210 width=$layout.data.barcode_width_sn}
    {/if}
{/if}
{if !$is_pdf || ($elements.label_barcode_product.display ne 'none' && $is_pdf)}
<span id="label_barcode_product">{$lng.lbl_product}</span>
{/if}
{if !$is_pdf || ($elements.barcode_product.display ne 'none' && $is_pdf)}
<span id="barcode_product">{$product.product}</span>
{/if}
