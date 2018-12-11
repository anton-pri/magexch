{include_once_src file='main/include_js.tpl' src='js/popup_files.js'}
<div class="form-group">
	<label class="col-xs-12">
        {if $ge_id}<input type="checkbox" value="1" name="fields[distribution]" />{/if}
        {$lng.lbl_esd_distribution}
    </label>
    <div class="col-xs-12 col-md-6">
      <input type="hidden" name="distribution_filename" id="distribution_filename" />
      <input class="form-control" type="text" name="distribution" id="distribution" value="{$product.distribution}"{if $read_only} disabled{/if}/>
    </div>
{if !$read_only}
    <div class="col-xs-12 col-md-6">
      <div class="additional_field">
        <input class="btn btn-green" type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_files('distribution_filename', 'distribution');" />
      </div>
    </div>
{/if}
</div>
