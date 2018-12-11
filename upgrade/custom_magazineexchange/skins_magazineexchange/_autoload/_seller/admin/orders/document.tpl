{capture name=section}
<div class="block content-boxed">

<!-- Order number, date & previous & next order links [ -->

    <h4 style="color:black;">
        <div align="left"><b>{$document_label} Order #{$doc.display_id}</b></div>
    </h4>
    <h7>
        <div>{$lng.lbl_date}: {$doc.date|date_format:$config.Appearance.datetime_format}</div>
    </h7>
{*
      {if $doc_id_prev}<a href="index.php?target={$current_target}&doc_id={$doc_id_prev.doc_id}">&lt;&lt;&nbsp;{$document_label} #{$doc_id_prev.display_id}</a>{/if}
      {if $doc_id_next}{if $doc_id_prev} | {/if}<a href="index.php?target={$current_target}&doc_id={$doc_id_next.doc_id}">{$document_label} #{$doc_id_next.display_id}&nbsp;&gt;&gt;</a>{/if}
*}
  <br /><br />

<!-- Order number, date & previous & next order links ] -->

{jstabs name='doc_O_info'}
default_tab={$js_tab|default:"order"}

[order]
title="{lng name="lbl_doc_info_`$doc.type`"}"
template="admin/docs/doc.tpl"

[process]
title="{$lng.lbl_process}"
template="admin/docs/notes2.tpl"

{* [relations]
title="{$lng.lbl_relations}"
template="main/docs/relations.tpl" *}

{*
{if $addons.sn and ($doc.type eq 'O' or $doc.type eq 'I' or $doc.type eq 'S' or $doc.type eq 'G' or $doc.type eq 'C')}
[serial_numbers]
title="{$lng.lbl_serial_numbers}"
template="addons/sn/serials.tpl"
{/if}


{if ($doc.type eq 'P' or $doc.type eq 'R' or $doc.type eq 'Q') and $addons.barcode}
[bar_code]
title="{$lng.lbl_bar_codes}"
template="addons/barcode/print_preparation.tpl"
{/if}
 *}

{/jstabs}
{include file='admin/tabs/js_tabs.tpl'}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_orders}
