{include file="addons/flexible_import/flexible_import_menu.tpl" active="7"}
{capture name=section}

{capture name=block2}
<table width='100%' cellpadding='3' cellspacing='0' class="table table-striped">
<thead>
<tr>
<th width="20%">{$lng.lbl_date}</th>
<th width="25%">{$lng.lbl_filename}</th>
<th width="5%">{$lng.lbl_size|default:'Size'}</th>
<th width="10%">{$lng.lbl_active_products_count|default:'Active Products Count'}</th>
<th width="20%">{$lng.lbl_created_by|default:'Created By'}</th>
<th width="20%">{$lng.lbl_restore_backup|default:'Restore Backup'}</th>
</tr>
</thead>
{if $datahub_products_backups ne ''}
{foreach from=$datahub_products_backups item=pb}
{assign var='date_text' value=$pb.date|date_format:$config.Appearance.datetime_format}
<tr>
<td>{$date_text}</td>
<td><span>{$pb.filename}</span></td>
<td nowrap>{$pb.filesize}</td>
<td>{$pb.active_products_count|default:'<i>undefined</i>'}</td>
<td><span>{$pb.created_by}</span></td>
<td>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_restore|default:'Restore' href="javascript:if (confirm('Current products will be saved to a new backup and then will be restored backup saved on `$date_text` !')) window.location.href='index.php?target=datahub_products_backups&action=restore&restore_backup_id=`$pb.backup_id`';" style='btn-green'}
</td>
</tr>
{/foreach}
{else}
<tr><td colspan='6' align='center'>No backups found</td></tr>
{/if}
</table>

<br>
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_create_backup_now|default:'Create Backup Now' href="index.php?target=datahub_products_backups&action=new" style='btn-green push-15-r'}&nbsp;
<br><br>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block2 title=$lng.lbl_datahub_saved_products_backups|default:'Saved Products Backups'}


{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_datahub_products_backups|default:'Datahub Products Backups'}
