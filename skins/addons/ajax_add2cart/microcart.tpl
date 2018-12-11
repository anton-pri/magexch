{if $config.ajax_add2cart.ajax_add2cart_enable_microcart eq 'Y'}
<script type="text/javascript">
    {assign_session var='cart' assign='cart'}
    var count_products_in_cart = '{$cart.products|@count}';
    var mouse_in_microcart = 0;
    var is_cart = '{$is_cart}';
    var is_mobile = '{$is_mobile}';
    {if $config.ajax_add2cart.place_where_display_minicart eq 1}
    var show_minicart_top = true;
    {else}
    var show_minicart_top = false;
    {/if}
{literal}
    $(document).ready(function() {
        microcart_content_hide();

        if (
            show_minicart_top
            && count_products_in_cart != '0'
            && is_cart != 'Y'
            && is_mobile == '0'
        ) {
            if ($("#microcart_content").length) {
                $("#microcart_content").html("");
            }
            else {
                $("#microcart").after("<div id='microcart_content' class='microcart_content'></div>");
            }

            $("#microcart").mouseenter(function() {

                if (!$('#microcart_content').is(':visible') ) {

                    if ($("#microcart_content").html() == "") {
                        ajaxGet(current_location+'/index.php?target=cart&action=update&get_top_minicart=1', null, microcart_content_show);
                    }
                    else {
                        microcart_content_show();
                    }
                }
            });

            $("#microcart_content").mouseenter(function() {
                mouse_in_microcart = 1;
            }).mouseleave(function() {
                mouse_in_microcart = 0;
            });

            $("body").click(function() {

                if (!mouse_in_microcart) {
                    microcart_content_hide();
                }
            });
        }
    });

    function microcart_content_show() {
        $("#microcart_content").fadeIn(200);
    }

    function microcart_content_hide() {
        $("#microcart_content").fadeOut(200);
    }
{/literal}
</script>
{/if}
