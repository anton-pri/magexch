<div id="preloaded_staticpopup_999999" style="display:none;">
{$lng.txt_payment_attempt_popup_note}    
<!--
If the payment failed when an order was originally placed<br>
you may attempt it again but note the following:<br><br>
The payment status can take some time to update - but<br>
if it continues to show as Failed and you are sure the<br>
payment transaction DID complete successfully then don't<br>
attempt it again instead, <a href="mailto:admin@magazineexchange.co.uk" class="attempt_note">contact Magazine Exchnage</a><br><br>
If the original payment failed more than once you may see<br>
multiple duplicate orders with a 'Failed' status. Be sure to<br>
be sure to only attempt payment again on one of these.<br>
-->
<table style="border-collapse: collapse; width: 100%;"><tbody><tr><td style="border: 0px solid rgb(0, 0, 0);"><br><br><br><br></td></tr></tbody></table>

<div class="close_container" style="display: flex">
  <div class="close_button" {literal}onclick="javascript: $('#cms_staticpopup_dialog').dialog('close');"{/literal}></div>
  <div 
    class="attempt_button" 
    onclick="{literal}javascript: window.location.href='index.php?target=attempt_payment&doc_id=' + $('#preloaded_staticpopup_999999').attr('custom-data'){/literal}"
  >
  </div>
</div>

</div>
