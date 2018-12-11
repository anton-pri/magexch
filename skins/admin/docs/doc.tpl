<div class="block">
  {if $current_area eq 'A' && $is_merchant_password ne 'Y' && $config.Security.blowfish_enabled eq 'Y'}
  {capture name=section}
    <form action="{$catalogs.admin}/index.php?target=merchant_password" method="post" name="mpasswordform">
      <input type="hidden" name="doc_id" value="{$doc_id}" />
      {$lng.txt_enter_merchant_password_note}
      <br /><br />
      <table>
      <tr>
	 <td><font class="VertMenuItems">{$lng.lbl_merchant_password}</font></td>
	 <td><input type="password" name="mpassword" size="16" /></td>
	 <td><input type="submit" value="{$lng.lbl_enter_mpassword|strip_tags:false|escape}" /></td>
      </tr>
      </table>
    </form>
  {/capture}
  {include file="common/section.tpl" title=$lng.lbl_enter_merchant_password content=$smarty.capture.section}
  <br />
  {/if}

<!-- cw@order_actions [ -->

  <div class="block-header">
    <ul class="block-options">
      {foreach from=$doc.related_docs key=type item=none}
        <li>{lng name="lbl_doc_info_`$type`"}</li>
      {/foreach}
      <li>
            <a class="button" href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print_label" target=_blank><i class="si si-doc"></i> {$lng.lbl_print_address_label}</a>
           {* <!-- PDF converter does not work reliable
             <a href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print_pdf" target=_blank>{$lng.lbl_pdf_format}</a>
            -->
            *}
      </li>
      {if $current_area eq 'A'}
        <li>
            <a class="button" href="index.php?target={$current_target}&doc_id={$doc_id}&mode=edit"><i class="fa fa-edit"></i> {$lng.lbl_modify}</a>
        </li>
      {/if}
      {if $current_area ne 'A' && $doc.type eq 'I' && $doc.status eq 'P'}
        <li>
            <a class="button" href="index.php?target={$current_target}&doc_id={$doc_id}&mode=quote">{$lng.lbl_proceed_with_quote}</a>
        </li>
      {/if}
      {include file="main/docs/additional_actions.tpl"}
      {foreach from=$doc.related_docs key=type item=docs}
        <li>
        {foreach from=$docs item=rel_doc}
          <a class="button" href="index.php?target=docs_{$type}&mode=details&doc_id={$rel_doc.doc_id}">#{$rel_doc.display_id}</a>
        {/foreach}
        </li>
      {/foreach}
    </ul>
    <h3 class="block-title">
      {if $usertype ne "C"}
        <div align="left"><b>{$document_label} #{$doc.display_id}</b><br />{$lng.lbl_date}: {$doc.date|date_format:$config.Appearance.datetime_format}</div>
      {/if}
      {if $doc_id_prev}<a href="index.php?target={$current_target}&doc_id={$doc_id_prev.doc_id}">&lt;&lt;&nbsp;{$document_label} #{$doc_id_prev.display_id}</a>{/if}
      {if $doc_id_next}{if $doc_id_prev} | {/if}<a href="index.php?target={$current_target}&doc_id={$doc_id_next.doc_id}">{$document_label} #{$doc_id_next.display_id}&nbsp;&gt;&gt;</a>{/if}
    </h3>

  </div>
<!-- cw@order_actions ] -->

<!-- cw@order_block [ -->
  {include file='admin/docs/doc_layout.tpl'}
<!-- cw@order_block ] -->
</div>
