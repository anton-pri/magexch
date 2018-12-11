{if !$id}
{assign var="id" value="image"}
{/if}
{if $mode eq 'advanced'}

<div class="input_field_1">
	<label>{$lng.lbl_word_verification}</label>

       <div class="float-left">
         <img src="{$app_web_dir}/index.php?target=antibot_image&section={$id}" id="{$id}" alt="" class='left' />
         <input type="text" placeholder='{$lng.lbl_type_the_characters}' name="antibot_input_str" class='required {if $antibot_err}error{/if}' />

         {if $antibot_err}<span class="field_error">&nbsp;&lt;&lt; Wrong verification code</span>{/if}<br />
         <a href="javascript: change_antibot_image('{$id}');">{$lng.lbl_get_a_different_code}</a><br />
         {if $is_flc}<br />
           <input type="hidden" name="login_antibot_on" value="1" />
         {/if}
       </div>
</div>
{elseif $mode eq 'simple'}
<div class="input_field_1">
	<label>{$lng.lbl_word_verification}</label>
    <input type="text" name="antibot_input_str"  placeholder='{$lng.lbl_type_the_characters}' class='required {if $antibot_err}error{/if} left'/>
 
	<div class="img_verification"> 
    <img src="{$app_web_dir}/index.php?target=antibot_image&section={$id}" id="{$id}"alt="" />
    {if $antibot_err}<span class="field_error">&nbsp;&lt;&lt;  Wrong verification code</span>{/if}
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
    <input type="text" name="antibot_input_str" />

    {if $antibot_err}<span class="field_error">&nbsp;&lt;&lt;</span>{/if}
</div>
{/if}
{if $antibot_err}
<script type="text/javascript">
$(document).ready(function() {ldelim}
$("input[name='antibot_input_str']").focus();
{rdelim});
</script>
{/if}
