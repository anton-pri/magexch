{if ($order.status eq "P" && !$order.credit) or $order.status eq "C"}
{$lng.lbl_paid}
{elseif $order.status eq "N"}
    {if $extended}
{$lng.lbl_expires_on} <b>{$order.exp_date|date_format:$config.Appearance.date_format}</b>
    {else}
{$lng.lbl_pay_invoice_now}
    {/if}
{elseif $order.status eq "E"}
   {if $extended}
{$lng.lbl_expired_on} {$order.exp_date|date_format:$config.Appearance.date_format}
    {else}
{$lng.lbl_expired}
    {/if}
{else}
{$lng.lbl_pending}
{/if}
