{if $value eq "I"}
{$lng.lbl_not_finished}
{elseif $value eq "Q"}
{$lng.lbl_queued}
{elseif $value eq "P"}
{$lng.lbl_processed}
{elseif $value eq "D"}
{$lng.lbl_declined}
{elseif $value eq "B"}
{$lng.lbl_backordered}
{elseif $value eq "F"}
{$lng.lbl_failed}
{elseif $value eq "C"}
{$lng.lbl_complete}
{elseif $value eq 'E'}
{$lng.lbl_expired}
{elseif $value eq 'N'}
{$lng.lbl_invoice_with_credit}
{/if}
