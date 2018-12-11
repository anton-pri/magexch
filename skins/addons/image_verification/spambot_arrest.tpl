{if !$id}
{assign var="id" value="image"}
{/if}
<div id ="spambot_arrest">
{if $mode eq 'advanced'}

<div class="input_field_1">
	<label>{$lng.lbl_word_verification}</label>

       <div class="float-left">
         <img src="{$app_web_dir}/index.php?target=antibot_image&section={$id}" id="{$id}" alt="" class='left' />
         <input type="text" id="antibot_input_str" placeholder='{$lng.lbl_type_the_characters}' name="antibot_input_str" class='required {if $antibot_err}error{/if}' />

         <span id="antibot_err_message" class="field_error" {if !$antibot_err}style="display:none"{/if}>&nbsp;&lt;&lt; Wrong verification code</span><br />
         <a href="javascript: change_antibot_image('{$id}');">{$lng.lbl_get_a_different_code}</a><br />
         {if $is_flc}<br />
           <input type="hidden" name="login_antibot_on" value="1" />
         {/if}
       </div>
</div>
{elseif $mode eq 'simple'}

<div class="input_field_1"> 
	<label class='required'>{$lng.lbl_word_verification}</label>
    <input type="text" id="antibot_input_str" name="antibot_input_str"  placeholder='{$lng.lbl_type_the_characters}'>

	<div class="verification_img"> 
    <img src="{$app_web_dir}/index.php?target=antibot_image&section={$id}" id="{$id}"alt="" />
    <span id="antibot_err_message" class="field_error" {if !$antibot_err}style="display:none"{/if}>&nbsp;&lt;&lt;  Wrong verification code</span>
    <br />
    <a href="javascript: change_antibot_image('{$id}');">{$lng.lbl_get_a_different_code}</a>
	</div>
</div>

{elseif $mode eq 'simple_column'}

<div class="input_field_1">
	<label>{$lng.lbl_type_the_characters}</label>
	<img src="{$app_web_dir}/index.php?target=antibot_image&section={$id}" id="{$id}"alt="" /><br />
	<a href="javascript: change_antibot_image('{$id}');">{$lng.lbl_get_a_different_code}</a>
    <br />
    <input type="text" id="antibot_input_str" name="antibot_input_str" />

    <span id="antibot_err_message" class="field_error" {if !$antibot_err}style="display:none"{/if}>&nbsp;&lt;&lt;</span>
</div>
{/if}
<input type="hidden" name="spambot_ajax_check" value="N" />
</div>

<script type="text/javascript">
<!--
$(document).ready(function() {ldelim}
$("input[name='antibot_input_str']").on('keyup',function(){ldelim}$('#antibot_err_message').hide();{rdelim});
if (typeof(requiredFields) == 'object')
requiredFields.push(new Array('antibot_input_str', "{$lng.lbl_word_verification|strip_tags|replace:'"':'\"'}", false));
{if $antibot_err}
$("input[name='antibot_input_str']").focus();
{/if}
{rdelim});
-->
</script>

{if $current_target eq 'popup_sendfriend'}
<div id="spambot_ajax_check_res" style="display: none"></div>
<script type="text/javascript">
<!--
{literal}
var spambot_parent_formname = '';
function cw_spambot_form_check(formname) {
    $('#spambot_ajax_check_res').html('');
    $("input[name='spambot_ajax_check']").val('Y');
    submitFormPart('spambot_arrest', cw_spambot_arrest_check);
    spambot_parent_formname = formname;
}
function cw_spambot_arrest_check() {
    $("input[name='spambot_ajax_check']").val('N');
    if ($('#spambot_ajax_check_res').text() == 'Y')  {
        $('#antibot_err_message').show();
        $("input[name='antibot_input_str']").focus();
    } else {
        cw_submit_form(spambot_parent_formname);
    }
}
{/literal}
-->
</script>
{/if}
