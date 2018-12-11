<select name="{$name}">
<option value=''>{$lng.lbl_please_select}</option>
{if $type eq 'P' || $type eq 'Q' || $type eq 'R'}
<option value='P'>{$lng.lbl_doc_info_P}</option>
<option value='Q'>{$lng.lbl_doc_info_Q}</option>
<option value='R'>{$lng.lbl_doc_info_R}</option>
    {foreach from=$relations.P item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_P} #{$rel.display_id}</option>
    {/foreach}
    {foreach from=$relations.Q item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_Q} #{$rel.display_id}</option>
    {/foreach}
    {foreach from=$relations.R item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_R} #{$rel.display_id}</option>
    {/foreach}
{elseif $type eq 'I' || $type eq 'O' || $type eq 'S'}
<option value='I'>{$lng.lbl_doc_info_I}</option>
<option value='O'>{$lng.lbl_doc_info_O}</option>
<option value='S'>{$lng.lbl_doc_info_S}</option>
<option value='C'>{$lng.lbl_doc_info_C}</option>
<option value='F'>{$lng.lbl_doc_info_F}</option>
    {foreach from=$relations.I item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_I} #{$rel.display_id}</option>
    {/foreach}
    {foreach from=$relations.O item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_O} #{$rel.display_id}</option>
    {/foreach}
    {foreach from=$relations.S item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_S} {$rel.display_id}</option>
    {/foreach}
{foreach from=$relations.C item=rel}
<option value='{$rel.doc_id}'>{$lng.lbl_doc_info_C} {$rel.display_id}</option>
{/foreach}
{/if}
</select>
