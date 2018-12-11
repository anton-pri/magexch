{*tunnel func='cw\news\get_available_newslists' via='cw_call' assign="is_news_exist"*}
{tunnel func='cw\news\get_available_newslists_home_customer' via='cw_call' assign="is_news_exist" lngcode=$shop_language}

{if $is_news_exist}
{capture name=menu}

{include file='customer/main/today_news.tpl'}

{include file='addons/news/subscribtion.tpl'}

{/capture}
{include file='common/menu.tpl' title=$lng.lbl_newsletter content=$smarty.capture.menu style="news"}
{/if}
{cms service_code="under_news"}
