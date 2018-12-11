<div class='form-group'>
<label class="col-xs-12">{$lng.lbl_ps_offers}:</label>
<div class="col-xs-12">
{include file='main/select/select.tpl' name='content_section[offers][]' multiple=1 data=$offers value=$cms_offers field_id='offer_id' field='title'}
</div>
</div>
