{capture name=dialog}


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="78%"><b>
<font color="#0A04FA" size="2"><br />{$lng.txt_seller_about_title_custom}</font></b><br /><br />
{$lng.txt_seller_about_title_custom_2}
<br /></td>
    <td width="22%" valign="top"><a href="#"><img src="{$AltImagesDir}/Help_Section_Images/Need_Help_Avatar.png" alt="Need Help? Click Here"></a></td>

  </tr>
</table>


<form action="http://www.magazineexchange.co.uk/huggins-email-form-script-v2.2.3.php5" 
      method="post" 
      id="example1">
      
<input name="FormEchoUser" type="hidden" value="yes">

<input name="FormEmailFieldList" type="hidden" value="email">

<input name="FormFieldNameLabelPlusList" type="hidden" value=
                "name, Name, Y, 0, 0 |
                 company, Publisher, Y, 0, 0 |
                                email, Email Address, Y, 0, 60 |
                 email2, Email Address, N, 0, 60 |
                 title, Magazine Title, Y, 0, 80 |
                 blankline,
                 ip, IP Address, N, 0, 0 |
                 browser, Browser Information, N, 0, 0 |
                 referer, Referer Page, N, 0, 0">
                 
<input name="FormFieldNameEditList" type="hidden" value=
                "">

<input name="Msg1FieldNameValueSubstitutionList" type="hidden" value=
                "current_pub, V1, No, 
                 current_pub, V2, Yes">

<input name="Msg1FieldNameValueSubstitutionList" type="hidden" value=
                "request, R1, Yes">

<input name="FormNameFieldList" type="hidden" value="name">

<input name="FormNextURL" type="hidden" value="admin/seller_form_completed.php">

<input name="Msg1AddrList" type="hidden" value="Magazine Exchange, j.arnold9, ntlworld.com">

<input name="Msg1FieldNameExcludeList" type="hidden" value=
                "email2">

<input name="Msg1Subject" type="hidden" value="About this Title page submission">

<input name="Msg1TextBottom" type="hidden" value="Review data and reply to Sender">

<input name="Msg1TextTop" type="hidden" value="Website ABOUT THIS TITLE - CUSTOM form has just been completed">

<input name="MsgEchoFieldNameExcludeList" type="hidden" value="*">

<input name="MsgEchoFromAddr" type="hidden" value="Magazine Exchange, content, magazineexchange.co.uk">

<input name="MsgEchoSubject" type="hidden" value="Your 'About this Title page' submission">

<input name="MsgEchoTextBottom" type="hidden" value="Thankyou for your information, which we will use to create the relevant 'About this title' page on the website.
Note that this is a manual process so does not happen immediately.">

<input name="MsgEchoTextTop" type="hidden" value="Thank you for submitting your 'About this Title' page information. This email is just to confirm that we have received your information, which is copied below for your convenience. Thankyou for your submission, which we will use to create the relevant 'About this title' page on the website. Note that this is a manual process so does not happen immediately.">

<input name="FormErrorPageTemplate" type="hidden" value="http://www.magazineexchange.co.uk/email-form-error-message.html">

<input name="FormErrorPageHeading1" type="hidden" value="Opps!, Arial, 8, black, yes">
<input name="FormErrorPageHeading2" type="hidden" value="The form you submitted contained some errors:, Arial, 5, black, no">
<input name="FormErrorPageTitle" type="hidden" value="Magazine Exchange - Email Form Error 
Page">
<input name="FormErrorPageLineClosing" type="hidden" value="Please use your brower's Back button to return to the form and correct the errors., Arial, 4, black, yes">
<input name="FormErrorPageLineOpening" type="hidden" value="The following errors were found:, Arial, 3, black, no">






<table width="700" border="0" cellspacing="0" cellpadding="4" align="center">


          <TR><TD VALIGN="TOP" ALIGN="RIGHT"><FONT SIZE="2"
          FACE="Arial,Helvetica,Verdana,Sans-serif">Your Name:</FONT></TD>
            <TD> 
              <INPUT TYPE="TEXT"
          NAME="name" SIZE="30" MAXLENGTH="50">
            </TD>

          </TR>

          <TR> 
            <TD VALIGN="TOP" ALIGN="RIGHT"><FONT SIZE="2"
          FACE="Arial,Helvetica,Verdana,Sans-serif">Email:</FONT></TD>
            <TD> 
              <INPUT TYPE="TEXT" NAME="email"
          SIZE="40" MAXLENGTH="60">
            </TD>
  
          </TR>

<TR><TD VALIGN="TOP" ALIGN="RIGHT"><br><FONT SIZE="2"
          FACE="Arial,Helvetica,Verdana,Sans-serif">Publisher name:</FONT>
</TD>
            <TD> 
              <INPUT TYPE="TEXT"
          NAME="company" SIZE="40" MAXLENGTH="60">
            </TD>
          </TR>



          <TR> 
            <TD VALIGN="top" ALIGN="RIGHT"><br>
        <font size="2" face="Arial,Helvetica,Verdana,Sans-serif">Magazine Title:</font><br><font size="2" face="Arial,Helvetica,Verdana,Sans-serif" color="#2F07FA">(Must already be listed on website). </font>

             
            </TD>
            <TD> 
             <INPUT TYPE="TEXT" NAME="title"
          SIZE="60" MAXLENGTH="80">
            </TD>
          </TR>


<tr>
<TD VALIGN="TOP" ALIGN="RIGHT"><br>
              <font size="2" face="Arial,Helvetica,Verdana,Sans-serif">Current publisher?</font>

            </TD>

<td align="left" valign="top"><br>
<input type="radio" value="V1" checked name="current_pub">
<font face="Tahoma" size="2">No, we are not the current publisher of this magazine title.<br>
<input type="radio" name="current_pub" value="V2"> Yes, we are the current publisher of this magazine title.<br>
</font>
</td>
</tr>


<tr>
<td colspan="3" align="middle">

<br /><br /><input type="radio" value="R1" checked name="request">
<font face="Tahoma" size="2">I request that a chargeable <b>Custom 'About this title'</b> page be set-up for the above magazine, and control assigned to our Seller account.<br>
</td>
</tr>


<tr>
<td colspan="3" align="middle">

<br /><br /><font style="font-family: Arial,Helvetica,sans-serif; font-size: 10pt;">
(Clicking "Submit" button is to accept our <a href="/pages.php?pageid=21" target="_blank"><b>Terms & Conditions</b>).</a></FONT>
</td>
</tr>

<tr>
<td colspan="3" align="middle">


<input type="hidden" name="submit" value="Send the Information" />
<font class="Button"><input type="image" src="{$AltImagesDir}/Submit_button.gif" alt="Registration - Seller Accounts" /></font>

</td>
</tr>
         
        </TABLE>  
<DIV><BR>


<hr></tr>
</table>

<input type="text" name="email2" size="1"></FORM>

{/capture}
{include file="common/section.tpl" title="About this title - Custom" content=$smarty.capture.dialog extra='width="100%"'}






