{jstabs}
default_tab={$js_tab|default:"order"}

[order]
title="{lng name="lbl_doc_info_`$doc.type`"}"
template="main/docs/doc.tpl"

[process]
title="{$lng.lbl_process}"
template="main/docs/notes.tpl"

{if $doc.type ne 'D'}
[relations]
title="{$lng.lbl_relations}"
template="main/docs/relations.tpl"
{/if}

{/jstabs}

{include file='tabs/js_tabs.tpl'}
