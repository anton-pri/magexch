{capture name=section}
{capture name=block}

<p><b>List of active visitor sessions. This page contains service information for technical staff.</b></p>
<p>{$lng.lbl_current_time}: {$current_time|date_format:$config.Appearance.datetime_format}</p>
<p>Sessions lifetime: {$config.Sessions.session_length} sec</p>
<table class="table table-striped dataTable vertical-center">
<thead>
<tr class="TableHead">
    <th>{$lng.lbl_user}</th>
    <th>IP</th>
    <th>{$lng.lbl_start}</th>
    <th>{$lng.lbl_expiry}</th>
    <th>Dump</th>
    <th>{$lng.lbl_kill} {$lng.lbl_session}</th>
</tr>
</thead>
{foreach from=$sessions item=session}
<tr class='{cycle values="cycle,"}{if $session.expiry<$current_time} expired{/if}'>
    <td>#{$session.customer_id} {$session.email}</td>
    <td>{$session.ip}</td>
    <td>{$session.start|date_format:$config.Appearance.datetime_format}</td>
    <td>{$session.expiry|date_format:$config.Appearance.datetime_format}</td>
    <td style="text-align: center;"><a href="index.php?target=sessions&action=details&sess_id={$session.sess_id}" target='_blank'>Dump</a></td>
    <td style="text-align: center;"><a href="index.php?target=sessions&action=kill&sess_id={$session.sess_id}">{$lng.lbl_kill}</a></td>
</tr>
{/foreach}
</table>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" title=$lng.lbl_sessions content=$smarty.capture.section}
