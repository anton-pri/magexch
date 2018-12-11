<div class="form-group">
        <label class="col-xs-12">{$lng.lbl_manufacturers}</label>
        <div class=" col-xs-12">
          {include file='addons/manufacturers/select/manufacturer.tpl' name='posted_data[advanced][manufacturer_id][]'  value=$search_prefilled.advanced.manufacturer_id is_please_select=0 multiple=7}
        </div>
</div>
