{include file="mail/mail_header.tpl"}

<p>
{$lng.eml_dear|substitute:"customer":$product.seller.name},
</p>

<p>
Your product "{$product.product}" has been approved and turned to status {if $status==1}"Enabled"{elseif $status==0}"Disabled"{else}{$status}{/if}.
<br />
Note, you can switch product status between enabled/disabled at any time. As soon as you switch it to "Pending for approval" you will have to wait again for admin approval before this product appears for selling.
</p>

{include file="mail/signature.tpl"}
