{include_once_src file='main/include_js.tpl' src='js/shortcut.js'}
<script language="javascript">

var dont_ask = 0;
function print_invoice() {ldelim}
    window.open('index.php?target=orders&doc_id={$doc_id}&mode=print_aom_pdf','order_preview','width=800, height=600, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no');
    print_pos(1);
{rdelim}

function print_pos(is_invoice) {ldelim}
    if (dont_ask == 0) {ldelim}
        dont_ask = 1;
        cw_doc_save_ajax('{$doc_id}', is_invoice);
    {rdelim}
{rdelim}

shortcut("F12",function() {ldelim}
    print_pos(0);
{rdelim});
</script>

{if !$order.saved}
<div class="button_left_align">
{include file='buttons/button.tpl' button_title=$lng.lbl_print_f12 href="javascript: print_pos(0)" style='button'}<br/>
</div>
{$lng.lbl_pos_doc_print_warning}

    {if $config.pos.is_use_printer eq 'Y'}
{include file='addons/pos/applet.tpl' params="`$catalogs.$app_area`/index.php?target=ajax&mode=aom&action=print&doc_id=`$doc_id`&`$APP_SESS_NAME`=`$APP_SESS_ID`"}
    {/if}
{/if}
