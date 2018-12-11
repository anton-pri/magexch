alter table cw_order_statuses add column email_message_customer_mode char(1) not null default 'I' after email_subject_admin;
alter table cw_order_statuses add column email_message_admin_mode char(1) not null default 'I' after email_message_customer_mode;
