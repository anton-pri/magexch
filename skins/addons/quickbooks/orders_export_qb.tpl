!ACCNT{$delimiter}NAME{$delimiter}ACCNTTYPE{$delimiter}SCD{$delimiter}EXTRA{$delimiter}HIDDEN
{* Define account for taxes. Please don't change ACCNTTYPE & EXTRA fields for it! It must be an income account. *}
ACCNT{$delimiter}{$config.quickbooks.acct_tax}{$delimiter}INC{$delimiter}{$delimiter}{$delimiter}N

{assign var="have_processed" value=""}

!CUST{$delimiter}NAME{$delimiter}BADDR1{$delimiter}BADDR2{$delimiter}BADDR3{$delimiter}BADDR4{$delimiter}BADDR5{$delimiter}SADDR1{$delimiter}SADDR2{$delimiter}SADDR3{$delimiter}SADDR4{$delimiter}SADDR5{$delimiter}PHONE1{$delimiter}FAXNUM{$delimiter}EMAIL{$delimiter}CONT1{$delimiter}SALUTATION{$delimiter}COMPANYNAME{$delimiter}FIRSTNAME{$delimiter}LASTNAME{$delimiter}CUSTFLD1

{section name=oid loop=$orders}
CUST{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$orders[oid].b_firstname} {$orders[oid].b_lastname}{$delimiter}{$orders[oid].b_address} {$orders[oid].b_address_2}{$delimiter}"{$orders[oid].b_city}, {$orders[oid].b_state} {$orders[oid].b_zipcode}"{$delimiter}{$orders[oid].b_countryname}{$delimiter}{$delimiter}{$orders[oid].s_firstname} {$orders[oid].s_lastname}{$delimiter}{$orders[oid].s_address} {$orders[oid].s_address_2}{$delimiter}"{$orders[oid].s_city}, {$orders[oid].s_state} {$orders[oid].s_zipcode}"{$delimiter}{$orders[oid].s_countryname}{$delimiter}{$delimiter}{$orders[oid].phone}{$delimiter}{$orders[oid].fax}{$delimiter}{$orders[oid].email}{$delimiter}{$orders[oid].lastname}, {$orders[oid].b_firstname}{$delimiter}{$orders[oid].title}{$delimiter}{$orders[oid].company}{$delimiter}{$orders[oid].firstname}{$delimiter}{$orders[oid].lastname}{$delimiter}{$orders[oid].customer_id}
{/section}

!TRNS{$delimiter}TRNSTYPE{$delimiter}DATE{$delimiter}ACCNT{$delimiter}NAME{$delimiter}CLASS{$delimiter}AMOUNT{$delimiter}DOCNUM{$delimiter}MEMO{$delimiter}ADDR1{$delimiter}ADDR2{$delimiter}ADDR3{$delimiter}ADDR4{$delimiter}ADDR5{$delimiter}SHIPVIA{$delimiter}SADDR1{$delimiter}SADDR2{$delimiter}SADDR3{$delimiter}SADDR4{$delimiter}SADDR5{$delimiter}TOPRINT

!SPL{$delimiter}TRNSTYPE{$delimiter}DATE{$delimiter}ACCNT{$delimiter}NAME{$delimiter}CLASS{$delimiter}AMOUNT{$delimiter}DOCNUM{$delimiter}MEMO{$delimiter}PRICE{$delimiter}QNTY{$delimiter}INVITEM{$delimiter}TAXABLE{$delimiter}EXTRA
!ENDTRNS

{if $config.quickbooks.export_invitems eq "Y"}
{* Declare invetory items *}
!INVITEM{$delimiter}NAME{$delimiter}INVITEMTYPE{$delimiter}DESC{$delimiter}PURCHASEDESC{$delimiter}ACCNT{$delimiter}PRICE{$delimiter}COST{$delimiter}TAXABLE{$delimiter}REORDERPOINT{$delimiter}ASSETACCNT{$delimiter}COGSACCNT
{section name=oid loop=$orders}
{if $orders[oid].gc_id}{* INVITEM:GIFT CERTIFICATE *}
INVITEM{$delimiter}GIFT CERTIFICATE{$delimiter}INVENTORY{$delimiter}GC#{$orders[oid].gc_id}{$delimiter}GC#{$orders[oid].gc_id}{$delimiter}{$config.quickbooks.acct_gc}{$delimiter}{$orders[oid].amount}{$delimiter}0{$delimiter}N{$delimiter}0{$delimiter}{$config.quickbooks.acct_inv_asset}{$delimiter}{$config.quickbooks.acct_inv_cogs}
{else}{* INVITEM:PRODUCT *}
{if $orders[oid].productcode ne ""}{assign var="invitem_name" value=$orders[oid].productcode}
{else}{assign var="invitem_name" value=$orders[oid].product_id}
{/if}
INVITEM{$delimiter}{$invitem_name}{$delimiter}INVENTORY{$delimiter}{$orders[oid].product}{if $orders[oid].product_options ne ""}\n{$orders[oid].product_options}{/if}{$delimiter}{$orders[oid].product}{if $orders[oid].product_options ne ""}\n{$orders[oid].product_options}{/if}{$delimiter}{$config.quickbooks.acct_product}{$delimiter}{$orders[oid].price}{$delimiter}0{$delimiter}N{$delimiter}{$config.quickbooks.qb_reporderpoint}{$delimiter}{$config.quickbooks.acct_inv_asset}{$delimiter}{$config.quickbooks.acct_inv_cogs}
{/if}
{/section}
{/if}{* $config.quickbooks.export_invitems eq "Y" *}

{assign var="new_order" value=""}
{section name=oid loop=$orders}
{if $orders[oid].total gt 0}
{assign var="order_total" value=$orders[oid].total}
{else}
{math equation="x+y" x=$orders[oid].total y=$orders[oid].info.giftcert_discount assign="order_total"}
{/if}

{if $new_order ne $orders[oid].doc_id}{* start new order *}
{if $new_order ne ""}
{* make qb happy *}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_tax}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}0{$delimiter}{$orders[oid].display_id}{$delimiter}TAX{$delimiter}{$delimiter}{$delimiter}{$delimiter}N{$delimiter}AUTOSTAX
ENDTRNS
{/if}
{assign var="new_order" value=$orders[oid].doc_id}
TRNS{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}Accounts Receivable{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}{$order_total}{$delimiter}{$orders[oid].display_id}{$delimiter}Website Order: {$orders[oid].details|replace:"\r":" "|replace:"\n":" "|replace:"\t":" "}{$delimiter}{$orders[oid].b_firstname} {$orders[oid].b_lastname}{$delimiter}{$orders[oid].b_address} {$orders[oid].b_address_2}{$delimiter}"{$orders[oid].b_city}, {$orders[oid].b_state} {$orders[oid].b_zipcode}"{$delimiter}{$orders[oid].b_countryname}{$delimiter}{$delimiter}{$delimiter}{$orders[oid].s_firstname} {$orders[oid].s_lastname}{$delimiter}{$orders[oid].s_address} {$orders[oid].s_address_2}{$delimiter}"{$orders[oid].s_city}, {$orders[oid].s_state} {$orders[oid].s_zipcode}"{$delimiter}{$orders[oid].s_countryname}{$delimiter}{$delimiter}Y

SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_shipping}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}-{$orders[oid].shipping_cost}{$delimiter}{$orders[oid].display_id}{$delimiter}{$orders[oid].shipping|trademark}{$delimiter}{$orders[oid].shipping_cost}{$delimiter}-1{$delimiter}SHIPPING{$delimiter}N{$delimiter}

{if $orders[oid].coupon_discount gt 0}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_coupon_discount}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}{$orders[oid].coupon_discount}{$delimiter}{$orders[oid].display_id}{$delimiter}{$orders[oid].coupon}{$delimiter}-{$orders[oid].coupon_discount}{$delimiter}-1{$delimiter}COUPON DISCOUNT{$delimiter}N{$delimiter}
{/if}

{if $orders[oid].discount gt 0}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_discount}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}{$orders[oid].discount}{$delimiter}{$orders[oid].display_id}{$delimiter}DISCOUNT{$delimiter}-{$orders[oid].discount}{$delimiter}-1{$delimiter}DISCOUNT{$delimiter}N{$delimiter}
{/if}

{foreach key=tax_name item=tax from=$orders[oid].tax_values}
{if $tax.tax_cost gt 0}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_tax}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}-{$tax.tax_cost}{$delimiter}{$orders[oid].display_id}{$delimiter}{$tax.tax_display_name} {$tax.rate_type}{$tax.rate_value}{$delimiter}{$vat_value}{$delimiter}-1{$delimiter}TAX{$delimiter}N{$delimiter}
{/if}
{/foreach}

{/if}{* start new order *}
{if $orders[oid].gc_id}{* INVOICE:GIFT CERTIFICATE *}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_gc}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}-{$orders[oid].amount}{$delimiter}{$orders[oid].display_id}{$delimiter}GC#{$orders[oid].gc_id}{$delimiter}{$orders[oid].amount}{$delimiter}-1{$delimiter}GIFT CERTIFICATE{$delimiter}N{$delimiter}
{else}{* INVOICE:PRODUCT *}
{if $orders[oid].productcode ne ""}{assign var="invitem_name" value=$orders[oid].productcode}
{else}{assign var="invitem_name" value=$orders[oid].product_id}
{/if}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_product}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}-{$orders[oid].cost}{$delimiter}{$orders[oid].display_id}{$delimiter}{$orders[oid].product}{if $orders[oid].product_options ne ""}\n{$orders[oid].product_options}{/if}{$delimiter}{$orders[oid].price}{$delimiter}-{$orders[oid].amount}{$delimiter}{$invitem_name}{$delimiter}N{$delimiter}
{/if}

{if $orders[oid].total gt 0 and $orders[oid].info.giftcert_discount gt 0}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_gc_discount}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}{$orders[oid].info.giftcert_discount}{$delimiter}{$orders[oid].display_id}{$delimiter}{$orders[oid].info.applied_giftcerts}{$delimiter}-{$orders[oid].info.giftcert_discount}{$delimiter}-1{$delimiter}GIFT CERTIFICATE(s){$delimiter}N{$delimiter}
{/if}

{if %oid.last%}

{* make qb happy *}
SPL{$delimiter}INVOICE{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}{$config.quickbooks.acct_tax}{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$config.quickbooks.trans_class}{$delimiter}0{$delimiter}{$orders[oid].display_id}{$delimiter}TAX{$delimiter}{$delimiter}{$delimiter}{$delimiter}N{$delimiter}AUTOSTAX

ENDTRNS
{/if}
{/section}

{if $config.quickbooks.qb_exportpayments eq "Y"}
!TRNS{$delimiter}TRNSTYPE{$delimiter}DATE{$delimiter}ACCNT{$delimiter}NAME{$delimiter}AMOUNT{$delimiter}PAYMETH{$delimiter}DOCNUM
!SPL{$delimiter}TRNSTYPE{$delimiter}DATE{$delimiter}ACCNT{$delimiter}NAME{$delimiter}AMOUNT{$delimiter}DOCNUM
!ENDTRNS

{assign var="new_order" value=""}
{section name=oid loop=$orders}
{if ($orders[oid].status eq "P" || $orders[oid].status eq "C") and $new_order ne $orders[oid].doc_id}
{if $orders[oid].total gt 0}
{assign var="order_total" value=$orders[oid].total}
{else}
{math equation="x+y" x=$orders[oid].total y=$orders[oid].info.giftcert_discount assign="order_total"}
{/if}
{assign var="new_order" value=$orders[oid].doc_id}
TRNS{$delimiter}PAYMENT{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}Undeposited Funds{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}{$order_total}{$delimiter}{$orders[oid].payment_method}{$delimiter}{$orders[oid].display_id}
SPL{$delimiter}PAYMENT{$delimiter}{$orders[oid].date|date_format:"%m/%d/%Y"}{$delimiter}Accounts Receivable{$delimiter}{$orders[oid].b_lastname}, {$orders[oid].b_firstname} - {$orders[oid].email}{$delimiter}-{$order_total}{$delimiter}{$orders[oid].display_id}
ENDTRNS
{/if}
{/section}
{/if}{* $config.quickbooks.qb_exportpayments eq "Y" *}
