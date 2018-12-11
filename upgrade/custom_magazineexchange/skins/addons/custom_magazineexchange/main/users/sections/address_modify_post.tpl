{if $is_checkout && !$customer_id}
  {if $profile_fields.basic.email.is_avail}
<div class="input_field_{if $address_type eq 'main'}1{else}0{/if}">
{if $address_type eq 'main'}
    <label class='required'>{$lng.lbl_email}</label>
    <input type="email" class='required email{if $is_checkout} validate_existing_email_remote{/if}' name="update_fields[basic][__email]" maxlength="64" value="{$userinfo.email}" id="custom_enter_email"/>
    {if $fill_error.basic.email}<font class="field_error">&lt;&lt;</font>{/if}

<script type="text/javascript">
{literal}
$(document).ready(function() {
/* its hidden with pre/post hooks that added surronding div to this area
    $('input[name="update_fields[basic][email]"]').parent().hide();
    $('input[name="update_fields[basic][password]"]').parent().hide();
    $('input[name="update_fields[basic][password2]"]').parent().hide();
*/
    $("#custom_enter_email").on('keyup', 
        function() {
            $('input[name="update_fields[basic][email]"]').val(this.value);
//            $('input[name="update_fields[basic][password]"]').val(this.value);
//            $('input[name="update_fields[basic][password2]"]').val(this.value); 
            $('input[name="update_fields[basic][password]"]').val('anonymous-checkout-user');
            $('input[name="update_fields[basic][password2]"]').val('anonymous-checkout-user'); 

        });
});
{/literal}
</script>

{else}
    <label>&nbsp;</label>
{/if}
</div>
  {/if}
{/if}
