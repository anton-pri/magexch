alter table cw_order_statuses add column extra_admin_email varchar(255) not null default '' after email_message_admin_mode;
