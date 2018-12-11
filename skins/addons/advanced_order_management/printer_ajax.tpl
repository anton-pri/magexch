fp_logical_name={$config.pos.pos_device}
fp_method={$config.pos.pos_method}
fp_start_string={$config.pos.pos_printer_start}
fp_end_string={$config.pos.pos_printer_end}
fp_payment_type={if $doc.pos.paid_by_cc}6{else}1{/if}

message={$lng.lbl_doc_id} {$doc.doc_id}
items_amount={count value=$doc.products}
date={$doc.date|default:0|date_format:$config.Appearance.date_format}
salesman={$doc.info.salesman_customer_id|default:'none'}
display_id={$doc.display_id|default:0}
subtotal={$doc.info.taxed_subtotal|formatprice:'':'.'}00
total={$doc.info.total|default:0|formatprice:'':'.'}00
{foreach from=$doc.products key=index item=product}
it_descr{$index}={$config.pos.pos_item_format|substitute:"product":$product.product|substitute:"sku":$product.product_code|substitute:"ean":$product.ean|substitute:"supplier_code":$product.supplier_code|default:'none'}
it_total{$index}={math equation="amount*price" amount=$product.amount price=$product.taxed_clear_price assign="total"}{$total|default:0|abs_value|formatprice:'':'.'}00
it_qty{$index}={$product.amount|default:0}
it_price{$index}={$product.taxed_clear_price|default:0|abs_value|formatprice:'':'.':3}00
{/foreach}
