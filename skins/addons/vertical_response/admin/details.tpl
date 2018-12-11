{if $list_id}
	{assign var="selector_disabled" value="1"}
{else}
	{assign var="selector_disabled" value="0"}
{/if}

{capture name=section}
	<form action="index.php?target={$current_target}&list_id={$list_id}" method="post" name="news_details_form">
		<input type="hidden" name="action" value="modify" />
		<input type="hidden" name="list[list_id]" value="{$list.list_id}" />

		{include file='main/select/edit_lng.tpl' disabled=true}

		<div class="clear"></div>

		<div class="box">
			<div class="input_field_1">
				<label>{$lng.lbl_news_list_short_name}</label>
				<input type="text" name="list[name]" value="{$list.name|escape}" />
				{if $error.name}<font class="field_error">&lt;&lt;{/if}
			</div>
			<div class="input_field_1">
				<label>{$lng.lbl_list_description}</label>
				<textarea name="list[descr]" cols="70" rows="10">{$list.descr}</textarea>
				{if $error.descr}<font class="field_error">&lt;&lt;{/if}
			</div>
		</div>
		<div class="buttons">
			{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('news_details_form');" acl='__2600'}
		</div>
	</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_create_news}
