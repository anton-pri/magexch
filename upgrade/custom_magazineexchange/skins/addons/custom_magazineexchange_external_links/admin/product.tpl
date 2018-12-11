{capture name=section}
<div class="box">
<div class="dialog_title">{$lng.txt_external_links_top_text|default:'txt_external_links_top_text'}</div>

<form action="index.php?target={$current_target}" method="post" name="update_external_links">
    <input type="hidden" name="mode" value="external_links" />
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="product_id" value="{$product.product_id}" />
    <input type="hidden" name="js_tab" value="external_links" />

    {*<table class="header" width="100%">*}
    <table class="table dataTable vertical-center" width="100%">
      <thead>
        <tr valign="top">
            <th width="5%"><input type='checkbox' class='select_all' class_to_select='external_link_item' /></th>
            <th>Seller</th>
            <th>Profile link</th>
            <th>URL</th>
            <th>Format</th>
            <th>Price</th>
{*            <th>Comment</th>*}
            <th>Category</th>
            <th>Action</th>
            <th>Value</th>
        </tr>
      </thead>
{if $external_links}
    {foreach from=$external_links item=link}
    {cycle values='class="table-striped-cycle", ' assign='external_links_row_style'}
    <tr {$external_links_row_style}>
        <td align="center"><input type="checkbox" value="Y" name="links[{$link.id}][delete]" class="external_link_item" /></td>
        <td><input type='text' style='width:100%' name='links[{$link.id}][seller]'   value='{$link.seller}' /></td>
        <td><input type='text' style='width:100%' name='links[{$link.id}][profile]'  value='{$link.profile}' /></td>
        <td><input type='text' style='width:100%' name='links[{$link.id}][link]'     value='{$link.link}' /></td>
        <td><input type='text' style='width:100%' name='links[{$link.id}][format]'   value='{$link.format}' /></td>
        <td><input type='text' class='micro'       name='links[{$link.id}][price]'      value='{$link.price}' size="8"/></td>
        <td><input type='text' style='width:100%' name='links[{$link.id}][category]' value='{$link.category}' /></td>
        <td><input type='text' style='width:100%' name='links[{$link.id}][action]'  value='{$link.action}' /></td>
        <td><input type='text' class='micro'       name='links[{$link.id}][value]'   value='{$link.value}' /></td>
    </tr>
    <tr {$external_links_row_style}> <td colspan="9">
        Comment:<br /><input type='text' style='width:100%' name='links[{$link.id}][comment]'   value='{$link.comment|escape}' title="Comment" placeholder="Comment"/>
    </td></tr>
 
    {/foreach}
    <tr class="cycle"><td colspan="9">
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_external_links');" button_title=$lng.lbl_update class="btn-green push-5-r"}
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('update_external_links', 'delete');" button_title=$lng.lbl_ppd_delete_selected class="btn-danger push-5-r"}
    </td></tr>
{else}
        <tr>
            <td colspan="10" align="center">No external links</td>
        </tr>

{/if}
    </table>
</form>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_external_links}

{capture name=section}
<div class="box">
<form action="index.php?target={$current_target}" method="post" name="add_external_link">
    <input type="hidden" name="mode" value="external_links" />
    <input type="hidden" name="action" value="add" />
    <input type="hidden" name="product_id" value="{$product.product_id}" />
    <input type="hidden" name="js_tab" value="external_links" />

    <table class="table dataTable vertical-center" width="100%">
       <thead>
        <tr valign="top">
{*            <th>&nbsp;</th>*}
            <th width='100'>Seller</th>
            <th>Profile link</th>
            <th>URL</th>
            <th>Format</th>
            <th>Price</th>
            {*<th>Comment</th>*}
            <th>Category</th>
            <th>Action</th>
            <th>Value</th>
        </tr>
      </thead>
    <tr class="table-striped-cycle">
{*        <td align="center">&nbsp;</td>*}
        <td><input type='text' style='width:100%' name='link[seller]' /></td>
        <td><input type='text' style='width:100%' name='link[profile]' /></td>
        <td><input type='text' style='width:100%' name='link[link]' /></td>
        <td><input type='text' style='width:100%' name='link[format]' /></td>
        <td><input type='text' class='micro'       name='link[price]' size="8"/></td>
        <td><input type='text' style='width:100%' name='link[category]' /></td>
        <td><input type='text' style='width:100%' name='link[action]'  /></td>
        <td><input type='text' class='micro'       name='link[value]' /></td>
    </tr>
    <tr class="table-striped-cycle">
        <td colspan="8">
        Comment:<br /><input type='text' style='width:100%' name='link[comment]'  /></td>
    </tr>
    <tr>
        <td colspan="8">
        {include file='admin/buttons/button.tpl' href="javascript:cw_submit_form('add_external_link');" button_title=$lng.lbl_add class="btn-green push-5-r"}
        </td>
    </tr>
    </table>
</form>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_add_new}
