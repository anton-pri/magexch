{if $product.product_type eq '4' && $product.avail gt 0}
    <div id="report_about_sold">
        <a href="javascript: void(0);" onclick="report_about_sold('{$product.product_id}');">{$lng.lbl_report_as_sold}</a>
    </div>

<script type="text/javascript">
<!--
{literal}
    function report_about_sold(id) {
        ajaxGet("index.php?target=report_about_sold&product_id=" + id, "report_about_sold");
    }
{/literal}
-->
</script>
{/if}
