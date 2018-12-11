{capture name="section"}
{capture name="block"}
<div class="dialog_title">{$lng.txt_category_products_top_text}</div>
{include file='common/subheader.tpl' title="`$lng.lbl_current_category`: `$current_category.category`"}
{if !$current_category.status}
<div class="ErrorMessage">{$lng.txt_category_disabled}</div>
{/if}

<div align="right">
{include file='buttons/button.tpl' href="index.php?target=categories&mode=edit&cat=`$current_category.category_id`" button_title=$lng.lbl_modify style="btn btn-green push-20 push-10-r"}
{include file='buttons/button.tpl' href="index.php?target=process_category&cat=`$current_category.category_id`&mode=delete" button_title=$lng.lbl_delete style="btn btn-danger push-20"}
</div>

{include file='common/navigation_counter.tpl'}

    {if $products}
<form action="index.php?target=products" method="post" name="process_product_form">
<input type="hidden" name="mode" value="process" />
<input type="hidden" name="action" value="update" />
<input type="hidden" name="cat" value="{$current_category.category_id}" />
<input type="hidden" name="navpage" value="{$navpage}" />

{include file='common/navigation.tpl'}
<div class="box">
{include file='main/products/products.tpl' products=$products current_target='products'}
</div>

{include file='common/navigation.tpl'}

{if $usertype eq 'A'}
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'delete');" button_title=$lng.lbl_delete_selected style="btn btn-danger push-10-r" acl=$page_acl}
{/if}
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form');" button_title=$lng.lbl_update style="btn btn-green push-10-r" acl=$page_acl}
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'list');" button_title=$lng.lbl_modify_selected style="btn btn-green push-10-r" acl=$page_acl}

{if ($usertype eq 'P' and $accl.1008) or ($usertype eq 'A' and $accl.1202)}
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'export');" button_title=$lng.lbl_export style="btn btn-green push-10-r"} 
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'export_found');" button_title=$lng.lbl_export_all_found style="btn btn-green push-10-r"}
{/if}

{if $usertype eq 'A'}
<br /><br />
{$lng.txt_operation_for_first_selected_only}<br/>
<br/>
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'details');" button_title=$lng.lbl_preview_product style="btn btn-green push-10-r push-20"}
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'clone');" button_title=$lng.lbl_clone_product acl=$page_acl style="btn btn-green push-10-r push-20"}
{include file='buttons/button.tpl' href="javascript: cw_submit_form('process_product_form', 'links');" button_title=$lng.lbl_generate_html_links style="btn btn-green push-10-r push-20"}
{/if}
    {/if}
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" title=$lng.lbl_category_products content=$smarty.capture.section}
