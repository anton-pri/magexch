{capture name=block_after_import}
<div class="box">
<a target='_blank' href='index.php?target=magexch_categories_recals'>Recalculate Categories Paths</a>
<br><br>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block_after_import extra='width="100%"' title='After Import Scripts'}
