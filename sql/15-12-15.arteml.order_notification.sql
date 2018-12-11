delete from cw_config where name in ('eml_doc_admin_P','enable_order_notif','enable_init_order_notif','enable_init_order_notif_customer','eml_doc_P','eml_doc_C','eml_doc_D');
update cw_languages set value='Email to Order department' where name='lbl_admin_is_notified';
update cw_languages set value='Extra email addresses to send admin message (coma separated)' where name='lbl_extra_email_to_send_admin_message';

