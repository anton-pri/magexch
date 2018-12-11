<?php
require_once('header.php');
item::hub_copy_images_from_similar_wines();
SWE_store_feed::SWE_store_import_and_update();
DWB_store_feed::store_import();

BevAccessFeeds::update_item_xref();
Skurnik_feed::update_item_xref();
bowler_feed::update_item_xref();
Touton_feed::update_item_xref();
Polaner_feed::update_item_xref();
Domaine_feed::update_item_xref();
bear_feed::update_item_xref();
vision_feed::update_item_xref();
angels_share_feed::update_item_xref();
BWL_feed::update_item_xref();
EBD_feed::update_item_xref();
acme_feed::update_item_xref();
noble_feed::update_item_xref();
Cordon_feed::update_item_xref();
triage_feed::update_item_xref();
cellar_feed::update_item_xref();
vehr_feed::update_item_xref();
vias_feed::update_item_xref();
grape_feed::update_item_xref();
vinum_feed::update_item_xref();
verity_feed::update_item_xref();
wildman_feed::update_item_xref();
cw_import_feed::update_item_xref();

item::set_image_to_no_image_if_blank();
xfer_products_SWE::hub_delete_xfer_products_SWE();

xfer_products_SWE::hub_insert_xfer_from_store_SWE();


xfer_temp::hub_del_xfer_temp();
xfer_temp::hub_insert_xfer_from_feeds_SWE();
xfer_products_SWE::hub_update_xfer_from_feeds_SWE();
xfer_products_SWE::hub_update_xfer_from_item_SWE();
xfer_products_DWB::hub_delete_xfer_products_DWB();
xfer_products_DWB::hub_insert_xfer_from_store_DWB();
xfer_temp::hub_del_xfer_temp();
xfer_temp::hub_insert_xfer_from_feeds_DWB();
xfer_products_DWB::hub_update_xfer_from_feeds_DWB(); 
xfer_products_DWB::hub_update_xfer_from_item_DWB();
xfer_products_SWE::hub_set_hide_true_SWE();
xfer_products_DWB::hub_set_hide_true_DWB();
xfer_products_SWE::hub_set_status_for_avail_code_0_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_1_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_2_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_3_SWE();
xfer_products_SWE::hub_set_status_for_avail_code_4_SWE();

xfer_products_DWB::hub_set_status_for_avail_code_0_DWB();
xfer_products_DWB::hub_set_status_for_avail_code_1_DWB();
xfer_products_DWB::hub_set_status_for_avail_code_2_DWB();
xfer_products_DWB::hub_set_status_for_avail_code_3_DWB();
xfer_products_DWB::hub_set_status_for_avail_code_4_DWB();


xfer_products_SWE::hub_set_hide_true_for_certain_products_SWE();
xfer_products_DWB::hub_set_hide_true_for_certain_products_DWB();
xfer_products_SWE::hub_SWE_hide_no_vintage_if_similar_with_vintage();
xfer_products_DWB::hub_DWB_hide_no_vintage_if_similar_with_vintage();
xfer_products_SWE::hub_hide_price_less_than_3_SWE();
xfer_products_SWE::hub_set_min_qty_price_threshold_SWE();

xfer_products_DWB::hub_hide_price_less_than_3_DWB();
xfer_products_DWB::hub_set_min_qty_price_threshold_DWB();

xfer_products_SWE::hub_set_domaine_min_3_SWE();
xfer_products_SWE::hub_set_bnp_min_6_SWE();
xfer_products_SWE::hub_set_polaner_min_3();
xfer_products_SWE::hub_set_angels_min();
xfer_products_SWE::hub_set_bear_min();
xfer_products_SWE::hub_set_vision_min();
xfer_products_SWE::hub_set_vias_min();
xfer_products_SWE::hub_set_verity_min();
xfer_products_SWE::hub_set_wildman_min();
xfer_products_SWE::hub_set_cw_import_min();

dwb_manual_price::update_dwb_price_from_xfer();
dwb_manual_price::dwb_manual_price_insert();
dwb_manual_price::update_dwb_manual_price_from_store_feed();
dwb_manual_price::dwb_manual_price_delete_stranded();

//open the table dwb_manual_price for editing

//map supplier id to xfer products
xfer_products_DWB::set_supplierid();
xfer_products_SWE::set_supplierid();
xfer_products_SWE::update_images();

xfer_products_SWE::update_twelve_bot_price_SWE();
xfer_products_SWE::update_meta_description();
xfer_products_SWE::update_keywords();
xfer_products_SWE::in_stock_sale_swe();

xfer_products_DWB::update_twelve_bot_price_DWB();
xfer_products_DWB::update_meta_description();
xfer_products_DWB::update_keywords();

xfer_products_SWE::set_twelvebot_price_to_zero();
xfer_products_SWE::set_twelvebot_cost_to_zero();

xfer_products_DWB::set_twelvebot_price_to_zero();
xfer_products_DWB::set_twelvebot_cost_to_zero();
echo "<br /><h2>Prepare Site Update Transfer Tables Complete.</h2>";
