update cw_breadcrumbs set title='lbl_card_types', parent_id=-1 where link='index.php?target=card_types';
update cw_languages set value='This section allows you to manage the credit card types that your store will be able to accept. Here you can create, modify and delete card types.' where name='txt_edit_cc_types_top_text';
update cw_languages set value="<a href='index.php?target=card_types'>Credit Card Types</a>" where name='txt_payment_methods_top_text';
