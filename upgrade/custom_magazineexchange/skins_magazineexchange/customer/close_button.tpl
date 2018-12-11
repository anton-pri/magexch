{if $page_data.type eq "staticpopup"}

<div class="close_container">
  <div class="close_button" onclick="javascript: close_button();"></div>
</div>

{literal}
<script type="text/javascript">
/*pagespeed_no_defer*/
function close_button () {
    $('.ui-dialog-titlebar-close').click();
}
</script>
{/literal}
{/if}
