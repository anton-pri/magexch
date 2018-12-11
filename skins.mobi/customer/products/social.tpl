{literal}
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
<a class="addthis_button_tweet"></a>
<a class="addthis_button_pinterest_pinit"></a>
<a class="addthis_counter addthis_pill_style"></a>
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50b7ac247b16ff5b"></script>
<!-- AddThis Button END -->
{/literal}
{if $config.Appearance.send_to_friend_enabled eq 'Y'}
<a href="javascript: window.open('index.php?target=popup_sendfriend&amp;product_id={$product.product_id}','SendToFriend','width=720,height=527,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no'); void(0);" class="send_to_friend">{$lng.lbl_email_this_to_friend}</a>
{/if}

