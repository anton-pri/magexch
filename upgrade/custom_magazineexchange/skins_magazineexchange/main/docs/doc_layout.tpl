{* 31cdcc51995182813c5edf273c3306511f95acad *}
{*include_once file='main/layout/css.tpl' load='web' layout=docs_`$doc.type`"*}

{assign var='info' value=$doc.info}
{assign var='userinfo' value=$doc.userinfo}
{assign var='profile_fields' value=$doc.userinfo.profile_fields}
{assign var='products' value=$doc.products}
{assign var='giftcerts' value=$doc.giftcerts}
{assign var='company' value=$doc.company_info}
{assign var='bank' value=$doc.bank_info}
{assign var='warehouse' value=$doc.warehouse_info}

{if $is_email_invoice ne 'Y'}
{$lng.lbl_customer_order_details_top_note}
<div class="order_block">
<br>
<div class="order_body">
<div class="doc_layout_top" id="doc_layout_top">
{include file='main/docs/layout/top.tpl'}
</div>
<div class="doc_layout_middle" id="doc_layout_middle">
{include file='main/docs/layout/middle.tpl'}
</div>
<div class="doc_layout_bottom" id="doc_layout_bottom">
{include file='main/docs/layout/bottom.tpl'}
</div>
</div>
</div>
{else}
{include file='main/docs/doc_layout_email.tpl'}
{/if}
