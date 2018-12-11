{capture name=section}
<div class="block content-boxed">
{jstabs name='doc_O_info'}
default_tab={$js_tab|default:"order"}

[order]
title="{lng name="lbl_doc_info_`$doc.type`"}"
template="admin/docs/doc.tpl"

[process]
title="{$lng.lbl_process}"
template="admin/docs/notes.tpl"

{* [relations]
title="{$lng.lbl_relations}"
template="main/docs/relations.tpl" *}

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

{/jstabs}

{include file='admin/tabs/js_tabs.tpl'}
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_orders}