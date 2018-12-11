{if $preview_area eq 'customer'}
<b>{$lng.lbl_customer_email_subject|default:'Customer email subject'}:</b><br />
{include file='mail/docs/status_changed_customer_subj.tpl'}
<hr />
<p />
<b>{$lng.lbl_custometr_email_body|default:'Customer email body'}:</b><br />
{include file='mail/docs/status_changed_customer.tpl'}
<p /><p />
{elseif $preview_area eq 'admin'}
<b>{$lng.lbl_admin_email_subject|default:'Admin email subject'}:</b><br />
{include file='mail/docs/status_changed_admin_subj.tpl'}
<hr />
<p />
<b>{$lng.lbl_admin_email_body|default:'Admin email body'}:</b><br />
{include file='mail/docs/status_changed_admin.tpl'}
<p /><p />
{/if}
