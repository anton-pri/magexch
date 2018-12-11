{if $addons.ppd ne '' && $current_target eq 'product' && $ppd_files ne ''}
{if $customer_id ne ''}{/if}
{if $ppd_files.after_purchase ne '' && $ppd_files.after_purchase|@count > 0}
<div class="files-group-header">{$lng.lbl_ppd_files_for_owner}</div>
<ul class="files-group">
	{foreach from=$ppd_files.after_purchase key=key item=file}
	<li>{if $file.fileicon ne ''}<img class="file-icon" src="{$file.fileicon|escape}" alt="{$file.type|escape}" />{/if}{$file.title|escape:html}{$lng.lbl_ppd_detailed_filesize|substitute:'size':"`$file.size`"}{if $file.hide_link != 1 && $file.perms_owner == 5}&nbsp;&nbsp;<a target="_blank" class="ppd-download-link" href="{$current_location}/index.php?target=getfile&amp;file_id={$file.file_id|escape:html}&amp;product_id={$product.product_id|escape:html}"
        title="{$lng.lbl_ppd_download|escape:html}">{$lng.lbl_ppd_download|escape:html}</a>{/if}</li>
	{/foreach}
</ul>
{/if}
{if $ppd_files.for_all ne '' && $ppd_files.for_all|@count > 0}
<div class="files-group-header">{$lng.lbl_ppd_files_for_all}</div>
<ul class="files-group">
	{foreach from=$ppd_files.for_all key=key item=file}
	<li>{if $file.fileicon ne ''}<img class="file-icon" src="{$file.fileicon|escape}" alt="{$file.type|escape}" />{/if}{$file.title|escape:html}{$lng.lbl_ppd_detailed_filesize|substitute:'size':"`$file.size`"}{if $file.perms_all == 5}&nbsp;&nbsp;<a target="_blank" class="ppd-download-link" href="{$current_location}/index.php?target=getfile&amp;file_id={$file.file_id|escape:html}&amp;product_id={$product.product_id|escape:html}"
        title="{$lng.lbl_ppd_download|escape:html}">{$lng.lbl_ppd_download|escape:html}</a>{/if}</li>
	{/foreach}
</ul>
{/if}
{/if}