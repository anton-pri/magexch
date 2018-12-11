alter table cw_order_statuses add column email_seller int(1) not null default 0 after email_admin;
alter table cw_order_statuses add column email_message_seller text not null default '' after email_subject_admin;
alter table cw_order_statuses add column email_subject_seller varchar(255) not null default '' after email_message_seller;
alter table cw_order_statuses email_message_seller_mode char(1) not null default 'I' after email_message_admin_mode;
alter table cw_order_statuses add column email_message_seller_mode char(1) not null default 'I' after email_message_admin_mode;
