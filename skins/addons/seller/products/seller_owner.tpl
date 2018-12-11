{if $product.seller}
    <div style="padding: 20px; background-color: #E6E6E6;overflow: auto;">
        <h1>Listed By</h1><br>
        {assign var="image" value=$product.seller.avatar}
        {assign var="contact_seller_href" value="index.php?target=message_box&mode=new&contact_id=`$product.seller.id`"}
        <div style="float: left; padding-right: 10px;">
            {if $image.tmbn_url}
                <img src="{$image.tmbn_url}"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="main/images/property.tpl"}"/>
            {else}
                <img src="{$catalogs.customer}/index.php?target=image&type=customers_images&tmp=1"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="main/images/property.tpl"}"/>
            {/if}
        </div>
        <strong>
            <a href="index.php?target=search&mode=search&created_by={$product.seller.id}" title="{$lng.lbl_modify_profile|escape}">{$product.seller.name}</a>
        </strong><br>
        <span>
            {$product.seller.address}
        </span><br>
        {include file='buttons/button.tpl' button_title=$lng.lbl_contact_seller href=$contact_seller_href style="button"}
    </div>
{/if}
