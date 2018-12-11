
{capture name=dialog}


<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 

<img src="{$AltImagesDir}/Help_Section_Images/New_Promotion_Pages559043814.jpg" height="52" width="730">

    <td width="78%"><!-- <b>
<font color="#0A04FA" size="2"><br />{$lng.txt_seller_request_promotion_page}</font></b>--><br /><br />
{$lng.txt_seller_request_promotion_page_2}
<br /></td>
    <td width="22%"><a href="http://www.magazineexchange.co.uk/pages.php?pageid=489&mode=preview""><img src="{$AltImagesDir}/Help_Section_Images/Need_Help_Avatar.png" alt="Need Help? Click Here"></a></td>

  </tr>
</table>




<div align="center">
<iframe allowtransparency="true" src="http://form.jotformpro.com/form/42978203486969?yourUsername={$login}" frameborder="0" style="width:650px; height:652px; border:none;" scrolling="no">
</iframe></div>



{/capture}
{include file="common/section.tpl" title="New Page Request Form" content=$smarty.capture.dialog extra='width="100%"'}
