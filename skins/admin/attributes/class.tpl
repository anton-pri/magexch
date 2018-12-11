{*include file='common/page_title.tpl' title=$lng.lbl_feature_classes*}
{capture name=section}
{capture name=block}

<form action="index.php?target={$current_target}" method="post" name="attribute_class_modify_form" class="form-horizontal">
<input type="hidden" name="action" value="modify_class">
<input type="hidden" name="posted_data[attribute_class_id]" value="{$attribute_class.attribute_class_id}">
<div class="box">
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_name}</label>
    <div class="col-xs-12"><input class="form-control" type="text" name="posted_data[name]" value="{$attribute_class.name|escape}" /></div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_att_position}</label>
    <div class="col-xs-6"><input class="form-control" type="text" name="posted_data[orderby]" value="{$attribute_class.orderby}" /></div>
</div>
<div class="form-group">
    <label class="col-xs-12">
    	{$lng.lbl_att_class_is_default} 
    	<input type="checkbox" name="posted_data[is_default]" value="1" {if $attribute_class.is_default} checked{/if} />
    </label>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_attributes}</label>
    <div class="col-xs-12 col-lg-6">
    	<table class="multirow_table">
    		<tr>
        		<td id="dsl_box_0">{include file='admin/select/attribute.tpl' name='posted_data[attributes][0]' id="attributes_0" value=0 is_please_select=1 is_show=null}</td>
        		<td id="dsl_add_button"> {include file='main/multirow_add.tpl' mark='dsl'}</td>
    		</tr>
    	</table>
    </div>
    {if $attribute_class.attributes}
    <script type="text/javascript">
// kornev, checked element have to be last one - in other case the checked status will be cloned
    {foreach from=$attribute_class.attributes key=key item=elm}
        add_inputset_preset('dsl', document.getElementById('dsl_add_button'), false, [ 
            {ldelim}regExp: /attributes_{$key+1}/, value: '{$elm}'{rdelim}
        ]);
    {/foreach}
    {if $checked_element}
    document.getElementById('default_values_select_is_default_{$checked_element}').checked =true;
    {/if}
    </script>
    {/if}
</div>
</div>
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('attribute_class_modify_form')" style="btn-green push-20"}
</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_feature_classes}
