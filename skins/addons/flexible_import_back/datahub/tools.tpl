{include file="addons/flexible_import/flexible_import_menu.tpl" active="3"}

{capture name=section}

{capture name=block1}

<form name="pos_export_form" method="post" action="index.php?target=datahub_tools">
<input type="hidden" name="action" value="pos_export" />
<input type="checkbox" name="export_includes[new]" value="1" id="export_incl_new" /><label for='export_incl_new'>&nbsp;New</label><br />
<input type="checkbox" name="export_includes[changed]" value="1" id="export_incl_changed" /><label for='export_incl_changed'>&nbsp;Changed</label><br />
<input type="checkbox" name="export_includes[orphaned]" value="1" id="export_incl_orphaned" /><label for='export_incl_orphaned'>&nbsp;Orphaned</label><br /><br />
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_export|default:'Export' href="javascript:cw_submit_form('pos_export_form');" style='btn-green'}
</form>
<br />
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block1 title=$lng.lbl_export_to_pos|default:'Export to POS' inline_style_content="padding-top:0px;"}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_datahub_tools|default:'Datahub Tools'}
