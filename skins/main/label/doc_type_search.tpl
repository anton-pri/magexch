{if $docs_type eq 'O'}
{$lng.lbl_search_orders}
{elseif $docs_type eq 'S'}
{$lng.lbl_search_ship_doc}
{elseif $docs_type eq 'I'}
{$lng.lbl_search_invoice}
{elseif $docs_type eq 'C'}
{$lng.lbl_search_credit_note}
{elseif $docs_type eq 'G'}
{$lng.lbl_search_group_orders}
{elseif $docs_type eq 'P'}
{$lng.lbl_search_supplier_orders}
{elseif $docs_type eq 'B'}
{$lng.lbl_search_salesman_orders}
{/if}
