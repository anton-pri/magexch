{if $product.seller}
    <div class="box">
        {include file='common/subheader.tpl' title=$lng.lbl_seller}

        <div class="input_field_0">
            <label>{$lng.lbl_name}</label>
            <a href="index.php?target=user_V&mode=modify&user={$product.seller.id}" title="{$lng.lbl_modify_profile|escape}">{$product.seller.name}</a>
        </div>
    </div>
{/if}