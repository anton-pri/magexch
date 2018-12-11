{if $list_id}
{assign var="selector_disabled" value="1"}
{else}
{capture name="section"}
{capture name="block"}

{assign var="selector_disabled" value="0"}
{/if}

<script type="text/javascript">
{literal}
  $(document).ready(function(){
    $("#news_details_form").validate();
  });
{/literal}
</script>


{*include file='common/page_title.tpl' title=$lng.lbl_create_news*}
<h3 class="block-title push-15">{$lng.lbl_create_news}</h3>
<form action="index.php?target={$current_target}&list_id={$list_id}" method="post" name="news_details_form" id='news_details_form'>
<input type="hidden" name="action" value="modify" />
<input type="hidden" name="list[list_id]" value="{$list.list_id}" />

{include file='main/select/edit_lng.tpl' disabled=true}

<div class="clear"></div>

<div class="form-horizontal">

<div class="form-group">
	<label class='required col-xs-12' >{$lng.lbl_news_list_short_name}</label>
	<div class="col-xs-12">
		<input type="text" name="list[name]" value="{$list.name|escape}" class='required form-control'/>
		{if $error.name}<font class="field_error">&lt;&lt;{/if}
	</div>
</div>
<div class="form-group">
	<label class='required col-xs-12'>{$lng.lbl_list_description}</label>
	<div class="col-xs-12">
		<textarea name="list[descr]" cols="70" rows="10" class='required form-control'>{$list.descr}</textarea>
		{if $error.descr}<font class="field_error">&lt;&lt;{/if}
	</div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_memberships}</label>
    <div class="col-xs-12">
    	{include file='admin/select/membership.tpl' name='memberships[]' value=$list.memberships multiple=5}
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_usertype}</label>
    <div class="col-xs-12">
    	<select name='list[usertype]' class="form-control">
    		{tunnel func='cw_user_get_usertypes' assign='usertypes' via='cw_call'}
    		{foreach from=$usertypes key='utype' item='utitle'}
        		<option value='{$utype}' {if $list.usertype eq $utype}selected='selected'{/if}>{$utitle}</option>
    		{/foreach}
    	</select>
    </div>
</div>
<div class="form-group">
        <label class="col-xs-12">{$lng.lbl_homepage_subscribe}</label>
        <div class="col-xs-6 col-md-2">
        	{include file='admin/select/yes_no.tpl' name='list[subscribe]' value=$list.subscribe}
        </div>
</div>
{if $usertype ne 'B'}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_active}</label>
    <div class="col-xs-6 col-md-2">
    	{include file='admin/select/yes_no.tpl' name='list[avail]' value=$list.avail}
    </div>
</div>
{/if}

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_news_list_show_messages}:</label>
    <div class="col-xs-6 col-md-2">
    	{include file='admin/select/yes_no.tpl' name='list[show_as_news]' value=$list.show_as_news}
    </div>
</div>

</div>
<div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('news_details_form');" acl='__2600' style="btn-green push-20"}</div>
</form>
{if $list_id}
{else}
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_news_management}
{/if}