{if $target_newslist_id}
{tunnel func='cw\news\get_available_home_newslists' via='cw_call' assign="is_subscription_allowed"}
{else}
{tunnel func='cw\news\get_available_home_newslists' via='cw_call' assign="is_subscription_allowed"}
{/if}

{if $is_subscription_allowed}

<form action="{$current_location}/index.php?target=news" name="subscribeform" method="post">
<input type="hidden" name="subscribe_lng" value="{$shop_language}" />
<input type='hidden' name='action' value='subscribe' />
{if $target_newslist_id}<input type='hidden' name='target_newslist_id' value='{$target_newslist_id}' />{/if}
<div class="input_block">
<input type="text" name="newsemail" size="20" value="{$lng.lbl_your_email_}" onClick="javascript:this.value='';"/>
{include file='buttons/button.tpl' button_title=$lng.lbl_subscribe href="javascript: cw_submit_form('subscribeform');" style='small'}
</div>
<a href="{$current_location}/index.php?target=news" class="unsubscribe">{$lng.lbl_unsubscribe}</a>
</form>

{/if}
