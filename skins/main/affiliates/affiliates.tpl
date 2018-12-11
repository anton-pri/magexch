<div class="dialog_title">{$lng.txt_affiliates_tree_note}</div>
 
{if $usertype ne 'B'}
{capture name=section}
<form action="index.php?target=affiliates" method="get">
<div class="input_field_0">
	<label>{$lng.lbl_salesman_as_root}</label>
    {include file='main/select/salesman.tpl' name='affiliate' value=$affiliate}
</div>
<input type="submit" value="{$lng.lbl_select|strip_tags:false|escape}" />
</form>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_select}

{/if}
{if $affiliate || $usertype eq 'B'}
{include file='main/affiliates/affiliates_incl.tpl'}
{/if}
