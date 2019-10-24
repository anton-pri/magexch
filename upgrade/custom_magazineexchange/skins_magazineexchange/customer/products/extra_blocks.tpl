<div class="width50 left">
    <div class="product_det" style="margin-top: 20px;">
      <div class="product_det_title">
        {$lng.lbl_articles_and_features}
      </div>
      <div class="subtitle">{$product.fulldescr}
      </div>
      <div class="footerLink">{cms service_code="add_comments_improve_description" preload_popup="Y" page_link_override="Add Comments / Improve Description"}</div>

    </div>

    <div class="ProductDetLinks">
        <b class="interesting">{$lng.lbl_interested_in_this_page}</b>
{literal}
<script type="text/javascript">
function bookmarksite(title,url){
if (window.sidebar) // firefox
    window.sidebar.addPanel(title, url, "");
else if(window.opera && window.print){ // opera
    var elem = document.createElement('a');
    elem.setAttribute('href',url);
    elem.setAttribute('title',title);
    elem.setAttribute('rel','sidebar');
    elem.click();
} else if(document.all)// ie
    window.external.AddFavorite(url, title);
}
</script>
{/literal}
        <div class="bookmark"><a onclick="bookmarksite(document.title, location.href);" style="width: 129px;"><img src="{$AltImagesDir}/bookmark.png" alt=""></a></div>
{literal}
<script type="text/javascript">
var send_to_friend_dialog_width = 450;
var send_to_friend_dialog_height = 260;
</script>
{/literal}

        {if $config.Appearance.send_to_friend_enabled eq 'Y'}
          <div class="email_to_friend"> <a href="index.php?target=popup_sendfriend&amp;product_id={$product.product_id}" class="ajax send_to_friend" id='send_to_friend_link' blockUI='send_to_friend_link'><img src="{$AltImagesDir}/email_to_friend.png" width="129" height="16"></a><div id='send_to_friend_dialog' style="display:none;"></div></div>
        {/if}

        <div class="clear"></div>
    </div>
</div>

<div class="width50 right">
    <div class="product_det" style="margin-top: 20px;">
      <div class="product_det_title">
        {$lng.lbl_article_snippets}<span class="right question">{cms service_code="article_snippet" preload_popup="Y"}</span>
      </div>
      <div class="subtitle">{$product.descr}</div>
    </div>

    <div class="product_det" style="margin-bottom: 0;">
      <div class="product_det_title">
        {$lng.lbl_adverts_and_links}<span class="right question">{cms service_code="adverts_popup" preload_popup="Y"}</span>

      </div>
      <div class="product_det_content auto_height border_bottom" style="padding-top: 5px;">
       {cms service_code='product_title_link' }<br>
         {tunnel func='magexch_get_attribute_value' via='cw_call' param1='P' param2=$product.product_id param3='magexch_product_keywords_extra_link' assign='product_keywords_extra_link'}
         {if $product_keywords_extra_link ne ''}
         <img src="{$product_keywords_extra_link}">
         {/if}  
       <br />
        <u>{$lng.lbl_buy_a_subscription}:</u><br /><br />
       {cms service_code='subscription_product' }
      </div>
      <div class="product_det_content auto_height border_bottom">
        <u>{$lng.lbl_advertisement}</u><br /><br />
       {cms service_code='product_context' }
       {*<a class="ProductBlue" href="">{$lng.lbl_advertise_your_business}</a>*}
       <div class="ProductBlue">{cms service_code='advertising_guide_intro'}</div>
      </div>
    </div>
</div>

