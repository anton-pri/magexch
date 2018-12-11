{capture name=section}
{capture name=block}

<form action='index.php?target=meta_tags' method='POST' name='meta_tags_form' class="form-horizontal">

<div class="box">

<input type='hidden' name='action' value='update'>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_title}</label>
    <div class="col-xs-12"><input type="text" name="meta[title]" value="{$meta_title|escape}" size="50" class="form-control"></div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_description}</label>
    <div class="col-xs-12"><textarea name="meta[descr]" cols="50" rows="6" class="form-control">{$meta_descr}</textarea></div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_keywords}</label>
    <div class="col-xs-12"><textarea name="meta[meta_keywords]" cols="50" rows="6" class="form-control">{$meta_keywords}</textarea></div>
</div>

</div>

<div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_update href="javascript: cw_submit_form('meta_tags_form');" style="btn-green push-20"}</div>
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_seo_setting}
