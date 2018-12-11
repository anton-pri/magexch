<form action="https://track.dhl-usa.com/TrackByNbr.asp?nav=TrackBynumber" method="post" name="getTrackNum" id="getTrackNum">
<input type="hidden" id="txtTrackNbrs" name="txtTrackNbrs" value="{$order.info.tracking|escape}" />
<input type="hidden" name="hdnErrorMsg" value="" />
<input type="hidden" name="hdnTrackMode" value="nbr" />
<input type="hidden" name="hdnPostType" value="init" />
<input type="hidden" name="hdnRefPage" value="0" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_dhl_redirection}
</form>
