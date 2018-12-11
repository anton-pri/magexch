{if $acc_products}
    <h2>{$list_title}</h2>
    <div {if $acc_products|@count >6}class="acc_recent product_slide"{else}class="acc_recent"  style="list-style:none"{/if} >
        {assign var="html_width" value="80"}

        {foreach from=$acc_products item=acc_product name=acc_p}

            <div    class="acc_product">
                <div class="acc_product_main">
                    <div class="acc_thumbnail" style="width: 100%;">
                        <center>
                            <a href="index.php?target=product&amp;product_id={$acc_product.product_id}">
                                {include file='common/product_image.tpl' product_id=$acc_product.product_id no_img_id='Y'}
                            </a>
                        </center>
                    </div>
                    <div class="fields">
                        <div class="product_field cycle link_to">
                            <a href="index.php?target=product&amp;product_id={$acc_product.product_id}">{$acc_product.product|truncate:25}</a>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>

                {if $addons.estore_products_review}
                    <div class="float-left">{include file='addons/estore_products_review/product_rating.tpl' rating=$acc_product.rating}</div>
                {/if}

                <div class="product_field taxed_price ">
                    <input name="product_accessories[{$acc_product.product_id}][price]" value="{$acc_product.display_price}" type="hidden">
                    <span id="product_accessories_product_price_{$acc_product.product_id}">{include file='common/currency.tpl' value=$acc_product.display_price plain_text_message=true}</span>
                    <span id="product_accessories_product_alt_price_{$acc_product.product_id}"> </span>
                </div>

            </div>

        {/foreach}
    </div>
{/if}
<div class="clear"></div>
