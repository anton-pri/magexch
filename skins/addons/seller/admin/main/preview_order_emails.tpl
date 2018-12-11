{if $preview_area eq 'seller'}
<b>{$lng.lbl_seller_email_subject|default:'Seller email subject'}:</b><br />
{include file='mail/docs/status_changed_seller_subj.tpl'}
<hr />
<p />
<b>{$lng.lbl_custometr_email_body|default:'Seller email body'}:</b><br />
{include file='mail/docs/status_changed_seller.tpl'}
<p /><p />
{/if}

