{if $help_page_title eq ''}
{if $section eq "general"}
{assign var=help_page_title value=$lng.lbl_help_zone }
{elseif $section eq "password"}
{assign var=help_page_title value=$lng.lbl_forgot_password }
{elseif $section eq "contactus"}
{assign var=help_page_title value=$lng.lbl_contact_us }
{elseif $section eq "faq"}
{assign var=help_page_title value=$lng.lbl_faq }
{elseif $section eq "business"}
{assign var=help_page_title value=$lng.lbl_privacy_statement }
{elseif $section eq "conditions"}
{assign var=help_page_title value=$lng.lbl_terms_n_conditions }
{elseif $section eq "about"}
{assign var=help_page_title value=$lng.lbl_about_our_site }
{elseif $section eq "login_customer"}
{assign var=help_page_title value=$lng.lbl_log_in }
{/if} 
{/if}

{capture name="dialog"}
{include file="help/main/`$section`.tpl"}
<!-- cw@help_main_section -->
{/capture}
{include file='common/section.tpl' is_dialog=1 content=$smarty.capture.dialog additional_class="help" title="$help_page_title"}
