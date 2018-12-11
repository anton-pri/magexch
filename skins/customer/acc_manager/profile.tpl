{include_once_src file='main/include_js.tpl' src='js/register.js'}

<script type="text/javascript">
{literal}
  $(document).ready(cw_register_init);
{/literal}
</script>
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}

<form name="profile_form" id="profile_form" action="{$form_url}/index.php?target={$current_target}" method="post" enctype="multipart/form-data">
<input type="hidden" name="js_tab" id="form_js_tab" value="">
<input type="hidden" name="action" value="update">

{foreach from=$profile_sections key=section_name item=section}
    {if $section.is_avail}
        {capture name='profile_section'}
            {if !$section.is_default}
                {include file="main/users/sections/custom.tpl" included_tab=$section.name }
            {else}
                {include file="main/users/sections/`$section_name`.tpl" included_tab=$section.name}
            {/if}
        {/capture}
        {include file='common/section.tpl' is_dialog=0 content=$smarty.capture.profile_section title=$section.section_title}
    {/if}
{/foreach}

<div class="buttons_container">
  {include file="buttons/button.tpl" button_title=$lng.lbl_save href="javascript: cw_submit_form('profile_form');" style='button'}
<!-- cw@customer_profile_orders_button [ -->
  {include file="buttons/button.tpl" button_title=$lng.lbl_orders_history href="index.php?target=docs_O" style='button'}
<!-- cw@customer_profile_orders_button ] -->
</div>

</form>
