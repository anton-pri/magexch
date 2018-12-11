{*include file='common/page_title.tpl' title=$lng.lbl_stop_list*}
{capture name=section}
{capture name=block}
<div class="box">
<div class="estore_container" id="estore_container_id">
    {include file='addons/estore_products_review/admin_stop_list_item.tpl' stop_list=$stop_list}
</div>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_stop_list}
<script type="text/javascript">
<!--
{literal}
    function delete_from_stop_list(id) {
        ajaxGet('index.php?target=estore_stop_list&mode=delete_from_stop_list&&review_id=' + id, 'estore_container_id');
    }
{/literal}
-->
</script>
