{capture name=section}

  {capture name=block}
  <div class="row">
    <div class="col-md-6 col-sm-12">
      <table class="table table-bordered">

        <tr>
          <td width="20%">{$lng.lbl_from}:</td>
          <td width="80%" align="left">
            <b>{$giftcert.purchaser|escape:"html"}</b>
          </td>
        </tr>

        <tr>
          <td>{$lng.lbl_to}:</td>
          <td align="left">
            <b>{$giftcert.recipient|escape:"html"}</b>
          </td>
        </tr>

        <tr>
          <td>{$lng.lbl_message}:</td>
          <td align="left">
            {$giftcert.message|escape:"html"|replace:"\n":"<br />"}
          </td>
        </tr>

        <tr>
          <td>{$lng.lbl_amount}:</td>
          <td align="left">
            <b>{include file='common/currency.tpl' value=$giftcert.amount}</b>
          </td>
        </tr>

        <tr>
          <td>{$lng.lbl_gc_template}:</td>
          <td align="left">
            <b>{$giftcert.tpl_file}</b>
          </td>
        </tr>



       {if $giftcert.send_via ne "P"}
       <tr>
         <td nowrap="nowrap">{$lng.lbl_email}:</td>
         <td align="left">
           <b>{$giftcert.recipient_email}</b>
         </td>
       </tr>
       {/if}



       {if $config.estore_gift.enablePostMailGC and $giftcert.send_via eq "P"}

       <tr>
         <td nowrap="nowrap">{$lng.lbl_firstname}:</td>
         <td align="left">
           <b>{$giftcert.recipient_firstname|escape:"html"}</b>
         </td>
       </tr>

       <tr>
         <td nowrap="nowrap">{$lng.lbl_lastname}:</td>
         <td align="left">
           <b>{$giftcert.recipient_lastname|escape:"html"}</b>
         </td>
       </tr>

       <tr>
         <td nowrap="nowrap">{$lng.lbl_address}:</td>
         <td align="left">
           <b>{$giftcert.recipient_address|escape:"html"}</b>
         </td>
       </tr>

       <tr>
         <td nowrap="nowrap">{$lng.lbl_city}:</td>
         <td align="left">
           <b>{$giftcert.recipient_city|escape:"html"}</b>
         </td>
       </tr>

       <tr>
         <td nowrap="nowrap">{$lng.lbl_zipcode}:</td>
         <td align="left">
           <b>{$giftcert.recipient_zipcode|escape:"html"}</b>
         </td>
       </tr>

       {if $config.General.use_counties eq "Y"}
       <tr>
         <td nowrap="nowrap">{$lng.lbl_county}:</td>
         <td align="left">
           <b>{$giftcert.recipient_countyname}</b>
         </td>
       </tr>
       {/if}

       <tr>
         <td nowrap="nowrap">{$lng.lbl_state}:</td>
         <td align="left">
           <b>{$giftcert.recipient_statename}</b>
         </td>
       </tr>

       <tr>
         <td nowrap="nowrap">{$lng.lbl_country}:</td>
         <td align="left">
           <b>{$giftcert.recipient_countryname}</b>
         </td>
       </tr>

       <tr>
         <td nowrap="nowrap">{$lng.lbl_phone}:</td>
         <td align="left">
           <b>{$giftcert.recipient_phone}</b>
         </td>
       </tr>

       {/if}

    </table>
  </div>
</div>

  <div class="form-group">
      {include file='buttons/button.tpl' button_title=$lng.lbl_go_back href="index.php?target=giftcerts" title=$title style="btn btn-default"}
  </div>
  {/capture}
  {include file="admin/wrappers/block.tpl" content=$smarty.capture.block title=$lng.lbl_gc_details}

{/capture}
{include file="admin/wrappers/section.tpl" title=$lng.lbl_view_gift_certificate content=$smarty.capture.section extra='width="100%"'}

