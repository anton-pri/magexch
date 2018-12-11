<script language="Javascript">
{literal}
function cw_docs_search(user) {
    fromdate = document.getElementById('user_docs_fromdate').value;
    todate = document.getElementById('user_docs_todate').value;
    doc_type = document.getElementById('user_customer_docs_type').value;
    document.getElementById('user_docs').src="index.php?target={/literal}{$current_target}{literal}&mode=docs&user="+user+"&fromdate="+fromdate+"&todate="+todate+"&doc_type="+doc_type;
}
{/literal}
</script>
<div class="input_field_0">
    <label>{$lng.lbl_date}</label>
{include file="main/select/date.tpl" name="user_docs_fromdate" value=$search_prefilled.admin.creation_date_start} -
{include file="main/select/date.tpl" name="user_docs_todate" value=$search_prefilled.admin.creation_date_start}
{include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_docs_search(`$user`)" js_to_href='Y'}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_document_type}</label>
    {include file='main/select/doc_type.tpl' name='user_customer_docs_type' multiple=5 value=$search_prefilled.admin.doc_type}
</div>

<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=docs&user={$user}" id="user_docs"></iframe>

{include file='main/users/sections/custom.tpl'}
