<select name="{$name}" {if $disabled} disabled{elseif $product}{edit_on_place table="products" pk=$product.product_id field="status"}{/if} >
    <option value='1'{if $value eq '1'} selected="selected"{/if}>{$lng.lbl_enabled}</option>
    <option value='0'{if $value eq '0'} selected="selected"{/if}>{$lng.lbl_disabled}</option>
    <option value='2'{if $value eq '2'} selected="selected"{/if}>Pending for approval</option>
</select>

