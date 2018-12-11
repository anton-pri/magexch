REPLACE INTO `cw_addons` (`addon`, `descr`, `active`) VALUES ('order_messages', 'Allows to perform communication between store admin and customers or suppliers', '0');
CREATE TABLE IF NOT EXISTS cw_order_messages_threads (
  `thread_id` int(11) not null auto_increment,
  `doc_id` int(11) not null default 0,
  `type` varchar(2) not null default 'M',
  PRIMARY KEY `thread_id` (thread_id)
);

CREATE TABLE IF NOT EXISTS cw_order_messages_messages (
  `message_id` int(11) not null auto_increment,
  `thread_id` int(11) not null default 0,
  `sender_id` int(11) not null default 0,
  `recepient_id` int(11) not null default 0,
  `date` int(11) not null default 0,
  `subject` varchar(255) not null default '',
  `body` text not null default '',
  `read_status` smallint(1) not null default 0,
  PRIMARY KEY `message_id` (message_id)
);

replace into cw_languages set name='eml_om_new_message', code='en', value='New message on order #{{order_id}}', topic='E-Mail';

delete from cw_config_categories where category='order_messages';
insert into cw_config_categories set category='order_messages';
SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'order_messages';

replace into cw_config set name='contact_email_address', comment='Email address for recieve and send order messages', type='text', config_category_id=@config_category_id, orderby = 10;
replace into cw_config set name='contact_email_password', comment='Email box password', type='text', config_category_id=@config_category_id, orderby = 20;
replace into cw_config set name='contact_email_access_info', comment='Email box access string (for example: {pop.gmail.com:995/novalidate-cert/pop3/ssl}INBOX)', type='text', config_category_id=@config_category_id, orderby = 30;

replace into cw_languages set name='addon_name_order_messages', code='en', value='Order Messages', topic='Addons';


replace into cw_languages set name='lbl_om_new_message', code='en', value='New message', topic='Label';
replace into cw_languages set name='lbl_om_initial_message', code='en', value='Initial message', topic='Label';
replace into cw_languages set name='lbl_om_no_new_messages', code='en', value='No new messages', topic='Label';
replace into cw_languages set name='lbl_om_unread_messages', code='en', value='Unread messages:{{unread_messages}}', topic='Label';
replace into cw_languages set name='lbl_om_communication_topics', code='en', value='Communication Topics', topic='Label';
replace into cw_languages set name='lbl_om_start_new_topic', code='en', value='Start New Topic', topic='Label';
 
