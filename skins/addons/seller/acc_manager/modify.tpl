{include_once_src file='main/include_js.tpl' src='js/register.js'}

<script type="text/javascript">
{literal}
  $(document).ready(cw_register_init);
{/literal}
</script>

{if $user}
    {assign var='profile_page_title' value=$lng.lbl_modify_profile}
{else}
    {assign var='profile_page_title' value=$lng.lbl_create_profile}
{/if}

{capture name='profile_page'}
<form name="profile_form" action="index.php?target={$current_target}&user={$user}" method="post" id='profile_form' enctype="multipart/form-data">
<input type="hidden" name="js_tab" id="form_js_tab" value="">
<input type="hidden" name="mode" value="{$mode}">
<input type="hidden" name="action" value="update">

{foreach from=$profile_sections key=section_name item=section}

    {if $section.is_avail}
        {capture name='profile_section'}
<div class="jasellerblock-content">
<div class="block">
            {if !$section.is_default}
                {include file="main/users/sections/custom.tpl" included_tab=$section.name }
            {else}
                {include file="main/users/sections/`$section_name`.tpl" included_tab=$section.name}
            {/if}
</div>
</div>
        {/capture}
        {include file='admin/wrappers/jablock.tpl' is_dialog=0 content=$smarty.capture.profile_section title=$section.section_title}
        <p />
    {/if}
{/foreach}

<p />

{include file="admin/buttons/button.tpl" button_title=$lng.lbl_save href="javascript: cw_submit_form('profile_form');" style="push-5-r btn-green"}
</form>
{/capture}
{include file='admin/wrappers/section.tpl' content=$smarty.capture.profile_page title=$profile_page_title}
