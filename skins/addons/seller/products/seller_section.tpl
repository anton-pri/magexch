{if $current_user_seller}
    <div style="overflow: auto;">
        {assign var="image" value=$current_user_seller.avatar}
        {assign var="contact_seller_href" value="index.php?target=message_box&mode=new&contact_id=`$current_user_seller.id`"}
        <div style="float: left; padding-right: 10px;">
            {if $image.tmbn_url}
                <img src="{$image.tmbn_url}"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="main/images/property.tpl"}"/>
            {else}
                <img src="{$catalogs.customer}/index.php?target=image&type=customers_images&tmp=1"{if $image.image_x ne 0} width="{$image.image_x}"{/if}{if $image.image_y ne 0} height="{$image.image_y}"{/if} alt="{include file="main/images/property.tpl"}"/>
            {/if}
        </div>
        <strong>
            {$current_user_seller.name}
        </strong><br>
        <span>
            {$current_user_seller.address}
        </span><br>
        {include file='buttons/button.tpl' button_title=$lng.lbl_contact_seller href=$contact_seller_href style="button"}
    </div>
{/if}