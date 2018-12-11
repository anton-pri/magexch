function handler_product_by_ean(data) {
    el = document.getElementById(data.el_product);
    if (el) el.innerHTML = data.product;
}

function cw_ean_check_product(ean, el_product) {
    $.ajax({ 
        "url":"index.php?target=ajax&mode=product_by_ean&ean="+ean+"&el_product="+el_product,
        "success":handler_product_by_ean, 
        "dataType":"json", 
        "type":"post", 
    });
}
