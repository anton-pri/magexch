{if $addons.interneka ne ""}
{if $config.interneka.interneka_per_lead eq "Y"}
<!-- begin of the link -->
<img src="https://interneka.com/affiliate/WIDLink.php?WID={$config.interneka.interneka_id}&amp;Payment=yes&amp;OrderID={$orders[oi].order.doc_id}" width="1" height="1" alt="" />
<!--- end of the link --> 
{/if}
{if $config.interneka.interneka_per_sale eq "Y"}
<!-- begin of the link -->
<img src="https://interneka.com/affiliate/WIDLink.php?WID={$config.interneka.interneka_id}&amp;TotalCost={$orders[oi].order.subtotal}&amp;OrderID={$orders[oi].order.doc_id}" width="1" height="1" alt="" />
<!--- end of the link -->
{/if}
{/if}
