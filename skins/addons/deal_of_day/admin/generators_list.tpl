{*include file='common/page_title.tpl' title=$lng.lbl_dod_manage_generators*}
{capture name=section}
<div class="dialog_title">{$lng.txt_dod_top_text}</div>

<form action="index.php?target={$current_target}" method="post" name="update_generators_form">
    <input type="hidden" name="mode" value="generators" />
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page" value="{$page}" />

    {if $dod_generators}
    {include file='common/navigation.tpl'}
    {/if}

<div class="box">

    <table class="header ps" width="100%">
        <tr valign="top">
            <th width="5%"><input type='checkbox' class='select_all' class_to_select='generator_item' /></th>
            <th width="5%">{$lng.lbl_dod_generator_position}</th>
            <th width="5%">{$lng.lbl_dod_generator_interval}</th>
            <th width="60%">{$lng.lbl_dod_generator_title}&nbsp;/&nbsp;{$lng.lbl_dod_generator_desc}</th>
            <th width="10%">{$lng.lbl_dod_generator_startdate}</th>
            <th width="10%">{$lng.lbl_dod_generator_enddate}</th>
            <th width="5%">{$lng.lbl_dod_generator_active}</th>
        </tr>

        {if $dod_generators}

        {foreach from=$dod_generators item=generator}
        <tr{cycle values=', class="cycle"'}>
            <td align="center"><input type="checkbox" value="Y" name="generator_ids[{$generator.generator_id}]" class="generator_item" /></td>
            <td align="center"><input type="text" size="6" maxlength="11" name="dod_generators[{$generator.generator_id}][position]" value="{$generator.position|default:0}" /></td>
            <td align="center"><input type="text" size="6" maxlength="11" name="dod_generators[{$generator.generator_id}][dod_interval]" value="{$generator.dod_interval|default:0}" /></td>
            <td><div><a href="index.php?target={$current_target}&amp;mode=details&amp;action=details&amp;generator_id={$generator.generator_id}" title="{$lng.lbl_dod_modify}">{$generator.title|escape}</a>
            {if $generator.exclusive eq 1}<span class="ps-exclusive-note">{$lng.lbl_dod_exlusive_note}</span>{/if}</div>
                {if $generator.description}<div>{$generator.description}</div>{/if}</td>
            <td align="center">{$generator.startdate|date_format:$config.Appearance.date_format|escape}</td>
            <td align="center">{$generator.enddate|date_format:$config.Appearance.date_format|escape}</td>
            <td align="center"><input type="checkbox" value="1" name="dod_generators[{$generator.generator_id}][active]"{if $generator.active eq 1} checked{/if} /></td>
        </tr>
        {/foreach}
        {if $navigation.total_pages gt 2}
        <tr>
            <td colspan="6">{include file='common/navigation.tpl'}</td>
        </tr>
        <tr>
            <td colspan="6" class="recs-delimiter"><img src="{$ImagesDir}/spacer.gif" height="5px" class="Spc" alt="" /></td>
        </tr>{/if}

        <tr>
            <td colspan="6" class="recs-delimiter"><img src="{$ImagesDir}/spacer.gif" height="10px" class="Spc" alt="" /></td>
        </tr>
        {else}
        <tr>
            <td colspan="6" align="center">{$lng.txt_dod_no_elements}</td>
        </tr>
        {/if}
    </table>
</div>


        <div class="buttons">
        {if $dod_generators}
                {include file='buttons/button.tpl' href="javascript:cw_submit_form('update_generators_form');" button_title=$lng.lbl_update}
                {include file='buttons/button.tpl' href="javascript:cw_submit_form('update_generators_form', 'delete');" button_title=$lng.lbl_dod_delete_selected}
        {/if}
                {include file='buttons/button.tpl' href="index.php?target=deal_of_day&action=form&generator_id=" button_title=$lng.lbl_add_new}
        </div>


</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_dod_manage_generators}
