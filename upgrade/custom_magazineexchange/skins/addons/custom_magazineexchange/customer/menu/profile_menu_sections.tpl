

{if $main eq "help" && $smarty.get.section eq "login_customer" || $main eq "acc_manager"}
{cms service_code="customer_account_left_banner"}
{else}


{capture name=menu}
    <ul>
      <li><a href='index.php?target=docs_O&mode=search'>{$lng.lbl_my_orders}</a></li>
      <li><a href='index.php?target=acc_manager'>{$lng.lbl_my_profile}</a></li>
      <li><a href='index.php?target=message_box'>{$lng.lbl_my_messages_received}</a></li>
      <li><a href='index.php?target=message_box&mode=sent'>{$lng.lbl_my_messages_sent}</a></li>
      <li><a href='index.php?target=message_box&mode=archive'>{$lng.lbl_my_messages_archived}</a></li>
    </ul>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_customer_account content=$smarty.capture.menu style='categories'}

{/if}