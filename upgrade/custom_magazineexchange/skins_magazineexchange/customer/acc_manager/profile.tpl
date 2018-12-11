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

<div class="seller_modify_form">
<div style="margin: 0 auto; width:460px;">

<form name="profile_form" id="profile_form" action="{$form_url}/index.php?target={$current_target}" method="post" enctype="multipart/form-data">
<input type="hidden" name="js_tab" id="form_js_tab" value="">
<input type="hidden" name="action" value="update">

{foreach from=$profile_sections key=section_name item=section}
    {if $section.is_avail}

{if $section.name eq 'address'}
{assign var='undertitle_text' value=$lng.lbl_edit_or_replace_your_existing_address}
{elseif $section.name eq 'basic'}
{assign var='undertitle_text' value=$lng.lbl_edit_your_profile_details_then_click_save}
{else}
{assign var='undertitle_text' value=''}
{/if}

        {capture name='profile_section'}
<div class="block">
            {if !$section.is_default}
                {include file="main/users/sections/custom.tpl" included_tab=$section.name }
            {else}
                {include file="main/users/sections/`$section_name`.tpl" included_tab=$section.name}
            {/if}
</div>
        {/capture}
        {*include file='common/section.tpl' is_dialog=0 content=$smarty.capture.profile_section title=$section.section_title*}
        {include file='customer/wrappers/jablock.tpl' is_dialog=0 content=$smarty.capture.profile_section title=$section.section_title undertitle_text=$undertitle_text}
    {/if}
{/foreach}

<div class="buttons_container">
  {include file="buttons/button.tpl" button_title=$lng.lbl_save href="javascript: cw_submit_form('profile_form');" style='button'}
<!-- cw@customer_profile_orders_button [ -->
  {include file="buttons/button.tpl" button_title=$lng.lbl_orders_history href="index.php?target=docs_O" style='button'}
<!-- cw@customer_profile_orders_button ] -->
</div>

</form>
</div>
<div class="profile_img" style="padding-right:48px">
        <a href="{$catalogs.customer}/help-centre-about-you-managing-your-account.html"><img src="{$AltImagesDir}/Avatar_Customer_My_Profile.gif" alt="Profile" /></a>
</div>
</div>
