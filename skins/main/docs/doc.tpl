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
<table width="100%">
<tr valign="top">
    <td>
{if $usertype ne "C"}
  <div align="left"><b>{$document_label} #{$doc.display_id}</b><br />{$lng.lbl_date}: {$doc.date|date_format:$config.Appearance.datetime_format}</div>
{/if}
{if $doc_id_prev}<a href="index.php?target={$current_target}&doc_id={$doc_id_prev.doc_id}">&lt;&lt;&nbsp;{$document_label} #{$doc_id_prev.display_id}</a>{/if}
{if $doc_id_next}{if $doc_id_prev} | {/if}<a href="index.php?target={$current_target}&doc_id={$doc_id_next.doc_id}">{$document_label} #{$doc_id_next.display_id}&nbsp;&gt;&gt;</a>{/if}
    </td>
    <td align="right">
    <table width="80%" class="header_bordered">
    <tr>
        <th>{$lng.lbl_actions}</th>
{foreach from=$doc.related_docs key=type item=none}
        <th>{lng name="lbl_doc_info_`$type`"}</th>
{/foreach}
    </tr>
    <tr valign="top">
        <td>
            <a href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print" target=_blank>{$lng.lbl_html_format}</a><br/>
           {* <!-- PDF converter does not work reliable
             <a href="index.php?target={$current_target}&doc_id={$doc_id}&mode=print_pdf" target=_blank>{$lng.lbl_pdf_format}</a><br/>
            -->
            *}
{if $current_area eq 'A'}
            <a href="index.php?target={$current_target}&doc_id={$doc_id}&mode=edit">{$lng.lbl_modify}</a><br/>
{/if}
{if $current_area ne 'A' && $doc.type eq 'I' && $doc.status eq 'P'}
            <a href="index.php?target={$current_target}&doc_id={$doc_id}&mode=quote">{$lng.lbl_proceed_with_quote}</a><br/>
{/if}
{include file="main/docs/additional_actions.tpl"}
        </td>
{foreach from=$doc.related_docs key=type item=docs}
        <td>
{foreach from=$docs item=rel_doc}
<a href="index.php?target=docs_{$type}&mode=details&doc_id={$rel_doc.doc_id}">#{$rel_doc.display_id}</a><br/>
{/foreach}
        </td>
{/foreach}
    </tr>
    </table>
    </td>
</tr>
</table>
<!-- cw@order_actions ] -->

<!-- cw@order_block [ -->
{include file='main/docs/doc_layout.tpl'}
<!-- cw@order_block ] -->
