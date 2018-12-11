<?php

cw_include('addons/news/include/func.news_list.php');

$cw_allowed_tunnels[] = 'cw\news\get_available_newslists';
$cw_allowed_tunnels[] = 'cw\news\get_available_home_newslists';
$cw_allowed_tunnels[] = 'cw\news\get_available_newslists_home_customer';
$cw_allowed_tunnels[] = 'cw\news\get_newslists';

cw_addons_set_controllers(
    array('replace', 'admin/news.php', 'addons/news/admin/news.php'),
    array('replace', 'customer/news.php', 'addons/news/news_manage.php'),
    array('replace', 'salesman/news.php', 'addons/news/salesman/news.php'),
    array('post', 'customer/auth.php', 'addons/news/news_last.php'), // TODO. It is too common file, specific controller must be hooked instead
    array('post', 'salesman/auth.php', 'addons/news/news_last.php'), // TODO. It is too common file, specific controller must be hooked
    array('post', 'customer/index.php', 'addons/news/customer/index.php'),
    array('replace', 'mail/unsubscribe.php', 'addons/news/mail/unsubscribe.php')
);

cw_event_listen('on_profile_modify','cw\news\on_profile_modify');

/*
cw_addons_set_template(
    array('post', 'customer/menu/menu_sections.tpl', 'addons/news/customer/menu/menu_sections.tpl')
);

cw_addons_set_template(
    array('replace', 'customer/main/welcome.tpl', 'addons/news/customer/index.tpl')
);
*/

