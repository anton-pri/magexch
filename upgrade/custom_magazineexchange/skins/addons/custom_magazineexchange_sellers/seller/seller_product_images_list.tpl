{assign var='seller_image_cell_width' value=150}
<!--
iM{$multiple} 
pic {$product_images_count}
fudc {$file_upload_data_count}
fud {$file_upload_data|@debug_print_var}
in_type {$in_type}
-->
{if $multiple || ($product_images_count le 0 && $file_upload_data_count le 0)}
<div class="seller_img_add" style="width: {$seller_image_cell_width}px; text-align:center;">
    <span>
        <input 
            type="button" 
            class="btn btn-minw btn-default btn-green push-5-t" 
            value="{$lng.lbl_add_image|default:'Add Image'|strip_tags:false|escape}" 
            onclick='javascript: popup_image_selection("{$in_type}", "0", "seller_product_images_{$in_type}_tmp","");' 
        />
    </span>
</div>
{/if}

{if $product_images_count gt 0}
<div class="image_saved" style="width: {$product_images_count*$seller_image_cell_width}px">
    {foreach from=$product_images item=image key=index}
        {if $in_type eq 'products_detailed_images'}
            {assign var='index2del' value=$image.image_id}
        {else}
            {assign var='index2del' value=$index}
        {/if}
        <div class="seller_image" id="saved_imgs_{$in_type}_{$index}">
            <img 
                src="{$image.tmbn_url}"
                {if $image.image_x ne 0} width="{$image.image_x}"{/if}
                {if $image.image_y ne 0} height="{$image.image_y}"{/if} 
                alt="{include file="main/images/property.tpl"}"
            />
            <div class="delete">
                <span>
                    <input 
                        type="button" 
                        class="btn btn-minw btn-default btn-green push-5-t" 
                        value="{$lng.lbl_delete}" 
                        onclick='javascript: seller_product_image_delete_{$in_type}({$index2del}, 1);' 
                    />
                </span>
            </div>
        </div>
    {/foreach}
</div>
{/if}

{if ($multiple || $product_images_count le 0) && $file_upload_data_count gt 0}
<div class="image_tmp" style="width: {$file_upload_data_count*$seller_image_cell_width}px">
    {foreach from=$file_upload_data key=index item=item}
        <div class="seller_image" id="tmp_imgs_{$in_type}_{$index}">
            <img 
                src="{$app_web_dir}/index.php?target=image&type={$in_type}&tmp=1&imgid={$index}&timestamp={if $file_upload_data.date}{$file_upload_data.date}{elseif $file_upload_data.$index.date}{$file_upload_data.$index.date}{/if}" 
                alt="{strip}{include file="main/images/property.tpl" image_data=$item}{/strip}" 
            />
            <div class="delete">
                <span>
                    <input 
                        type="button" 
                        class="btn btn-minw btn-default btn-green push-5-t" 
                        value="{$lng.lbl_delete}" 
                        onclick='javascript: seller_product_image_delete_{$in_type}({$index}, 0);' 
                    />
                </span>
            </div>
        </div>
    {/foreach}
</div>
{/if}