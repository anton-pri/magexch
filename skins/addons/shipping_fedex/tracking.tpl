<form name="tracking" action="https://www.fedex.com/Tracking">
<input type="hidden" name="ascend_header" value="1" />
<input type="hidden" name="clienttype" value="dotcom" />
<input type="hidden" name="cntry_code" value="us" />
<input type="hidden" name="language" value="english" />
<input type="hidden" name="tracknumbers" value="{$order.info.tracking|escape}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_fedex_redirection}
</form>
