{if ($usertype_layout ne 'A' && $usertype_layout ne 'V') || $current_area eq 'C'}
    {tunnel func='cw\custom_magazineexchange_sellers\mag_get_seller_digital_product_sale' via='cw_call'
        param1=$product.extra_data.seller_item.seller_item_id
        param2=$userinfo.customer_id|default:$doc.userinfo.customer_id
        assign='seller_product_sale'}
    {if $seller_product_sale.download_link}
        <br />
        {if in_array($status, array('I','F','D'))}
             {$lng.lbl_download_link_pending}
        {else}
            {if !$seller_product_sale.download_link_expired}
                <a title="{$lng.lbl_download}" href="{$seller_product_sale.download_link}" style="color:blue" target="_blank">{$lng.lbl_download}</a>
            {else}
                {$lng.lbl_download_link_expired|default:'Download link expired'}
            {/if}
        {/if}
    {/if}
{/if}
