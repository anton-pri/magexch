<form action="https://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do" method="post" name="getTrackNum" id="getTrackNum">
<input type="hidden" id="strOrigTrackNum" name="strOrigTrackNum" value="{$order.info.tracking|escape}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_usps_redirection}
</form>
