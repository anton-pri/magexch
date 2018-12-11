{if $config.Appearance.show_in_stock eq "Y" and $product.distribution eq ""}
    {if $product.avail gt 0}<b>{$product.avail} {$lng.lbl_items} </b><div class="in_stock">{$lng.lbl_in_stock}</div> {*<i>{$lng.txt_items_available|substitute:"items":$product.avail}</i>*}{else}<div class="out_of_stock">{$lng.lbl_out_of_stock}</div>{/if}
{/if}
