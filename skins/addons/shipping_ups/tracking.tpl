<form name="noname" method="post" action="https://wwwapps.ups.com/tracking/tracking.cgi">
<input name="accept_UPS_license_agreement" type="hidden" value="yes"  />
<input name="nonUPS_title" type="hidden" value="" />
<input name="nonUPS_header" type="hidden" value="" />
<input name="nonUPS_body" type="hidden" value="" />
<input name="nonUPS_footer" type="hidden" value="" />
<input name="tracknum" type="hidden" value="{$order.info.tracking|escape}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_ups_redirection}
</form>
