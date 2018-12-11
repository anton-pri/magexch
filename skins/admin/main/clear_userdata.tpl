{capture name=section}
    {capture name=block}
        <form method="POST" action="index.php?target=clear_userdata" name='clear_userdata_form'>
        <input type="hidden" name='action' />
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_clean_orders|default:'Clear Orders' href="javascript:if (confirm('All orders will be deleted!')) cw_submit_form('clear_userdata_form', 'delete_docs');" style='btn-danger push-15-r'} <p /><br>
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_clean_customers|default:'Clear Customers' href="javascript:if (confirm('All customers will be deleted!')) cw_submit_form('clear_userdata_form', 'delete_customers');" style='btn-danger push-15-r'} <p />
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_clean_sellers|default:'Clear Sellers' href="javascript:if (confirm('All sellers will be deleted!')) cw_submit_form('clear_userdata_form', 'delete_sellers');" style='btn-danger push-15-r'} <p />
            {include file='admin/buttons/button.tpl' button_title=$lng.lbl_clean_orders|default:'Clear Products' href="javascript:if (confirm('All products will be deleted!')) cw_submit_form('clear_userdata_form', 'delete_products');" style='btn-danger push-15-r'} <p /><br>

        <form
    {/capture}
    {include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_clear_userdata|default:'Clear User Data'}
