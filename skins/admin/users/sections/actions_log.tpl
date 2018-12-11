<script language="Javascript">
{literal}
function cw_actions_log_search(user) {
{/literal}
    fromdate = document.getElementById('user_actions_log_fromdate').value;
    todate = document.getElementById('user_actions_log_todate').value;
    document.getElementById('user_actions_log').src="index.php?target={$current_target}&mode=actions_log{if $current_area eq 'A'}&user="+user+"{/if}&fromdate="+fromdate+"&todate="+todate;
{literal}
}
{/literal}
</script>

<div class="input_field_0">
    <label>{$lng.lbl_date}</label>
{include file='main/select/date.tpl' name='user_actions_log_fromdate' value=$search_prefilled.admin.creation_date_start} -
{include file='main/select/date.tpl' name='user_actions_log_todate' value=$search_prefilled.admin.creation_date_end}
{include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_actions_log_search(`$user`)"}
</div>

<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=actions_log&user={$user}" id="user_actions_log"></iframe>

{include file='main/users/sections/custom.tpl'}
