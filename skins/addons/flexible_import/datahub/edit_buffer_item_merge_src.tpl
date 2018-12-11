<script>
var btable_id = '{$table_id}';
var mID = '{$merge_src_ID}';

$(document).ready(function () {ldelim}
{foreach from=$bmerge_config key=bfield item=mfield_data}
    {assign var='mfield' value=$mfield_data.mfield}
    $("#merge_src_{$table_id}_{$bfield|replace:'&':'and'}").html('{$merge_item_data.$mfield|escape:javascript}');
{/foreach}
{if $merge_item_data.image ne ''}
    $("#merge_src_{$table_id}_image").attr('src',"{$merge_item_data.image.web_path|escape:javascript}");
    $("#merge_src_{$table_id}_image_path").html("{$merge_item_data.image.web_path|escape:javascript}");
{/if}
{rdelim});

</script>
