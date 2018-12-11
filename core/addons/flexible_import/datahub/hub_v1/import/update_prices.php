<?php
require_once('header.php');
include('clean_money.php');

BWL_feed::update_item_xref();
BevAccessFeeds::update_item_xref();
Cordon_feed::update_item_xref();
//DWB_store_feed::update_item_xref();//this isn't included in this macro
//Domaine_feed::update_item_xref();//this isn't included in this macro
EBD_feed::update_item_xref();
Polaner_feed::update_item_xref();
Skurnik_feed::update_item_xref();
Touton_feed::update_item_xref();
acme_feed::update_item_xref();
angels_share_feed::update_item_xref();
bear_feed::update_item_xref();
bowler_feed::update_item_xref();
cellar_feed::update_item_xref();
noble_feed::update_item_xref();
triage_feed::update_item_xref();
vehr_feed::update_item_xref();
vision_feed::update_item_xref();
vias_feed::update_item_xref();
grape_feed::update_item_xref();
vinum_feed::update_item_xref();
verity_feed::update_item_xref();
cru_feed::update_item_xref();
cavatappi_feed::update_item_xref();
wildman_feed::update_item_xref();
cw_import_feed::update_item_xref();

SWE_store_feed::SWE_update_pos_cost();

item_price::pricing_del_item_price();
item_price::pricing_calc_SWE();
item_price_twelve_bottle::build_twelve_bottle_price();//added Jan 11
item_price::pricing_calc_DWB();
item::pricing_apply_surcharges();
item_price::pricing_set_BWL_max_markup();
item_price::pricing_round_to_4();
item_price::pricing_set_manual_price_SWE();
item_store2::hub_update_POS_PriceA();//this updates MSRP(price) but not xref

echo '<br /><br /><h2>Update prices is complete</h2>';

