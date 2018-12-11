{capture name=section}

<p>Please select content sections which should be shown for this product only.</p>
<p>The listed content section may have other restrictions such as categories, manufacturers, active date range.</p>
<form name='productContentSectionForm' method='POST' >
<input type="hidden" name="product_id" value="{$product_id}" />
<input type="hidden" name="action" value="ab_update" />
    <table width='100%' class="table table-striped dataTable vertical-center">
        <tr class="{cycle values=',cycle'}">
            <th width='20' align="left"><input type='checkbox' class='select_all' class_to_select='contentsection_item' /></th>
            <th>{$lng.lbl_cs_service_code}</th>
            <th>{$lng.lbl_cs_type}</th>
            <th>{$lng.lbl_cs_title}</th>
        </tr>

{foreach from=$contentsections item=contentsection}
    {assign var="edit_contentsection_link" value="index.php?target=cms&amp;mode=update&amp;contentsection_id=`$contentsection.contentsection_id`"}
    <tr class="{cycle values=',cycle'}">
    <td>
        <input type="checkbox" name="contentsections[{$contentsection.contentsection_id}]" value="1" {if $contentsection.selected}checked='checked'{/if} class="contentsection_item" />
    </td>
    <td><a href='{$edit_contentsection_link}'>{$contentsection.service_code}</a></td><td>{$contentsection.type}</td><td>{$contentsection.name}</td>
    </tr>
{/foreach}
    </table>

<div class="buttons bottom">
{include file="admin/buttons/button.tpl" href="javascript: cw_submit_form('productcontentsectionForm', 'ab_update');" button_title=$lng.lbl_update|escape acl=$page_acl style="btn-green push-20"}
</div>
</form>

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_cs_content_sections content=$smarty.capture.section}
