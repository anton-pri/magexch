<script language="javascript">
var submit_url = 'index.php?target=ajax&mode=cart_update&is_ajax=1';
var home_url = 'index.php';
var product_indexes = new Array();
{foreach from=$products item=product}
product_indexes['{$product.cartid}'] = 'productindexes_{$product.cartid}';
{/foreach}
var shipping_indexes = new Array();

{literal}
function cw_cart_update(request) {
    blockElements('cart_totals', false);
    var data = $.parseJSON(request.responseText);
    if (data.expired) {
        //window.location.href= home_url;
        return;
    }

    if (data.cart_items)
        for(i in data.cart_items) {
            if ($('#cart_item_price_'+i)) {
                $('#cart_item_price_'+i).html(data.cart_items[i].price);
                $('cart_item_total_'+i).html(data.cart_items[i].total);
                $('cart_item_alter_'+i).html(data.cart_items[i].alter);
                if ($('cart_item_taxes_'+i)) $('cart_item_taxes_'+i).html(data.cart_items[i].taxes);
                var item_special = $('#cart_item_special_'+i);
                if (item_special) item_special.html(data.cart_items[i].special);
                $('productindexes_'+i).value = data.cart_items[i].amount;
            }
        }
    if (data.cart_totals_arr)
        for(i in data.cart_totals_arr) {
            if (document.getElementById('cart_totals_'+i)) document.getElementById('cart_totals_'+i).innerHTML = data.cart_totals_arr[i];
        }

    if (data.grand_total) document.getElementById('grand_total').innerHTML = data.grand_total;
    if (data.cart_totals) document.getElementById('cart_totals').innerHTML = data.cart_totals;
}

function ajax_update_cart() {
    blockElements('cart_totals', true);
    var form = $('form[name=cartform]');
    $.ajax({
        url     : submit_url,
        data    : form.serialize()+'&shipping_id='+$('#shipping_id').val(),
        complete: cw_cart_update,
        dataType: "json",
        type    : "post"
    });
}
{/literal}
</script>
