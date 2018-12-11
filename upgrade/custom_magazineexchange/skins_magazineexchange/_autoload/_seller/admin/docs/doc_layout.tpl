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

<!-- <div class="block-header bg-green"><h3 class="block-title" style="text-align: center;">Order Details</h3></div> -->


{if $is_email_invoice ne 'Y'}
<div class="block-content block-content-narrow push-50">

<div style="background-color: #FCCB05;
    padding: 0px 20px 1px;
    max-width: 100%;
    overflow-x: visible;
 border: 8px solid #FCCB05;
">


 <div class="adminnotes">{$lng.lbl_note_order_details}</div>

<div style="background-color: white;
    padding: 20px 20px 1px;
    max-width: 100%;
    overflow-x: visible;
">

<div class="doc_layout_top" id="doc_layout_top">
{include file='admin/docs/layout/top.tpl'}
</div>
<div class="doc_layout_middle" id="doc_layout_middle">
{include file='admin/docs/layout/middle.tpl'}
</div>
<div class="doc_layout_bottom" id="doc_layout_bottom">
{include file='admin/docs/layout/bottom.tpl'}
</div>
</div>
</div>
</div>
{else}
{include file='main/docs/doc_layout_email.tpl' usertype_layout='V'}
{/if}
