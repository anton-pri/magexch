{if in_array($attribute.type, array('decimal','text','integer'))}
	{* ranges_for_scalar flag means we're editing additional range values which may be used in product filter *}
	{assign var='ranges_for_scalar' value=true}
{/if}
{capture name=section}
{capture name=block}

<p>{$lng.lbl_expl_text_for_feature_option_edit|default:'lbl_expl_text_for_feature_option_edit'}</p>
<p><a href="index.php?target=attributes&mode=att&attribute_id={$attribute.attribute_id}">{$lng.lbl_features_modify}</a></p>
<form action="index.php?target={$current_target}&mode=att" method="post" name="attribute_options_modify_form">
<input type="hidden" name="action" value="modify_att_options">

<input type="hidden" name="attribute_id" value="{$attribute.attribute_id}">

{if  $attribute.type eq 'selectbox' || $ranges_for_scalar}
<div class="input_field_0" id="default_value_select">
    {include file='admin/attributes/attribute_options_select.tpl' attribute=$attribute}
</div>
{elseif $attribute.type eq 'multiple_selectbox'}
<div class="input_field_0" id="default_value_multiselect">
    {include file='admin/attributes/attribute_options_multiselect.tpl' attribute=$attribute}
</div>
{/if}

</form>

<div id="sticky_content" class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('attribute_options_modify_form');" style='btn-green push-20'}
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"'}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_feature_options_modify|default:'Feature options modify'}
