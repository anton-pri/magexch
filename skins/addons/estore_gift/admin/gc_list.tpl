{*include file='common/page_title.tpl' title=$lng.lbl_gift_certificates*}
{capture name=section}

  {capture name=block}

    <p>{$lng.txt_gc_admin_top_text}</p>

<form action="index.php?target={$current_target}" method="post" name="gc_form">
<input type="hidden" name="action" value="" />
<div class="box">
<table class="table table-striped dataTable" width="100%">
  <thead>
    <tr>
	<th width="5" class="nowrap">Del<input type='checkbox' class='select_all checkbox-middle push-5-l' class_to_select='giftcerts_item' /></th>
       <th width="5" class="nowrap">{$lng.lbl_print}<input type='checkbox' class='select_all checkbox-middle push-5-l' class_to_select='giftcerts_item_p' /></th>
	<th width="10%" class="text-center">{$lng.lbl_order}</th>
	<th width="25%">{$lng.lbl_giftcert_ID}</th>
	<th width="10%" class="text-center">{$lng.lbl_gc_type}</th>
	<th width="10%">{$lng.lbl_status}</th>
	<th width="25%">{$lng.lbl_rem_amount}</th>
	<th width="20%">{$lng.lbl_added}</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$giftcerts item=gc}
    <tr{cycle values=", class='cycle'"}>
      <td class="text-center">{if $gc.doc.doc_id}&nbsp;{else}<input type="checkbox" id="{$gc.gc_id}" name="gc_ids[{$gc.gc_id}]" class="giftcerts_item" />{/if}</td>
      <td class="text-center"><input type="checkbox" name="gc_ids_p[{$gc.gc_id}]" class="giftcerts_item_p" /></td>
      <td class="text-center">
      {if $gc.doc_id}
        {if $gc.doc.doc_id}
          <a href="index.php?target=docs_O&doc_id={$gc.doc.doc_id}">#{$gc.doc.display_id}</a>
        {else}
          <acronym title="document #{$gc.doc_id} is deleted" >{$gc.doc_id}</acronym>
        {/if}
      {else}
	 {$lng.txt_not_available}
      {/if}
{*
{if $gc.related_docs}
    {foreach from=$gc.related_docs item=doc_type key=doc_key}
	    {foreach from=$doc_type item=doc}
			<a href="index.php?target=docs_{$doc_key}&doc_id={$doc.doc_id}">#{$doc.display_id}</a>
	    {/foreach}
    {/foreach}
{else}
	{$lng.txt_not_available}
{/if}
*}
      </td>
      <td class="font-w600"><a href="index.php?target={$current_target}&mode=modify_gc&amp;gc_id={$gc.gc_id}">{$gc.gc_id}</a></td>
      <td class="text-center">
        {if $gc.send_via eq "E"}{$lng.lbl_email}{else}{$lng.lbl_mail}{/if}
      </td>
      <td>
        {include file='main/select/gc_status.tpl' name="status[`$gc.gc_id`]" value=$gc.status}
      </td>
      <td>
        {include file='common/currency.tpl' value=$gc.debit}/{include file='common/currency.tpl' value=$gc.amount}  
	</td>
	<td>
        {$gc.add_date|date_format:$config.Appearance.datetime_format}
	</td>
    </tr>
  {foreachelse}
    <tr>
      <td colspan="7" class="text-center">{$lng.not_found}</td>
    </tr>
  {/foreach}
  </tbody>
</table>

{if $giftcerts}
	<p class="text-danger">{$lng.txt_gc_update_warning}</p>
	<div style="display: none" id="print_warn_box">
			<div id="print_warn_msg"></div><br>
		</div>
{/if}


</div>
<div class="buttons form-group">
  {if $giftcerts}
	{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('gc_form')" acl='__0802' style="btn btn-minw push-5-r btn-green"}
	{include file='buttons/button.tpl' button_title=$lng.lbl_delete_selected href="javascript: cw_submit_form('gc_form', 'delete');" acl='__0802' style="btn btn-minw push-5-r btn-danger"}
	{include file='buttons/button.tpl' button_title=$lng.lbl_print_selected href="javascript: cw_submit_form('gc_form','print');" style="btn btn-minw push-5-r btn-green"}
  {/if}
  {include file='buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=`$current_target`&mode=add_gc" acl='__0802'  style="btn btn-minw push-5-r btn-green"}
</div>
</form>

  {/capture}
  {include file="admin/wrappers/block.tpl" content=$smarty.capture.block }
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_gift_certificates}
