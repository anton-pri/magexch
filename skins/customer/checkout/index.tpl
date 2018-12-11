<div id="txt_conditions_customer" style="display: none;">{$lng.txt_conditions_customer}</div>
<div id="txt_privacy_statement" style="display: none;">{$lng.txt_privacy_statement}</div>

{include_once file='js/check_required_fields_js.tpl'}
{include_once file='main/include_js.tpl' src='js/register.js'}

<script type="text/javascript">
<!--
var txt_must_be_logged_for_order = "{$lng.txt_must_be_logged_for_order|escape:"javascript"}";
var lbl_osc_agree_terms_submit = "{$lng.lbl_osc_agree_terms_submit|escape:"javascript"}";
var to_block = {$from_quote};

var account_option = '{$config.Appearance.default_account_option}';
var is_checkout = 1;
{literal}
function cw_one_step_checkout_check_register(act) {

    if (!$('#terms_conditions').is(':checked')) {
        alert(lbl_osc_agree_terms_submit);
        return false;
    }

    if (!customer_id) {
        alert(txt_must_be_logged_for_order);
        return false;
    }

    // Copy fields from payment form to checkout form
    var target = $('#checkout_form');
    target.find('#copied_payment_data').remove();

    var paymentData = $('#onestep_payment').find(':input');
    var appendData = $(document.createElement('div'));
    appendData.attr('id','copied_payment_data');

    if (paymentData.length > 0) {
        paymentData.each(function() {
            $(document.createElement('input'))
                .attr('type','hidden')
                .attr('name', $(this).attr('name'))
                .val($(this).val())
                .appendTo(appendData);
        });
    }

    appendData.prependTo(target);

    // check copied fields
    if (check_cc_number == 'Y' && target.find('input[name=card_number]').length>0) {
        if(!checkCCNumber(document.checkout_form.card_number,document.checkout_form.card_type) ||
        !checkExpirationDate(document.checkout_form.card_expire_Month,document.checkout_form.card_expire_Year) ||
        !checkCVV2(document.checkout_form.card_cvv2,document.checkout_form.card_type))
            return false;
    }

// TODO: Rework checkout. register_form form should not be sumbitted used when an order submits. Not it is called from cw_one_step_checkout_check_register
//	cw_submit_form_ajax('register_form', 'cw_one_step_checkout_register');

    // Piece taken from cw_one_step_checkout_register
    $('btn_box').css('display', 'none');
    $('msg').css('display', 'none');

    if (act != '') {
        document.checkout_form.action.value = act;
    }
    document.checkout_form.submit();

	return true;
}

$(document).ready(function() {
    $('#onestep_option input:radio').change(function(){
        $('.osc-login').hide();
        $('#osc-login-'+$(this).val()).show();
    });
    
    $('#onestep_option input:radio').eq(account_option).click();

    $('#onestep_option_container > .width50 > div').equalHeights();

});


{/literal}
-->
</script>

<div id="box" class="message-dialog-box">
<div style="text-align:center"><span id="txt"></span></div>
</div>
<!-- cw@checkout_title [ -->
<h1>{$lng.lbl_checkout}</h1>
<!-- cw@checkout_title ] -->

{*<div id='opc-welcome'>{$lng.lbl_osc_welcome}</div>*}

<div id='opc-main'>
<ul id="opc-sections">

    <li id="onestep_option" class="opc-section enter">
    <!-- cw@checkout_enter [ -->

        <div class="osc-section"> 
        {*<h2>1) {$lng.lbl_do_you_have_account}</h2>*}
        <div class="grey_opc" id="onestep_option_container">
        <div class="float-left width50">
          <div class="checkout_block">
            <h2>{$lng.lbl_create_an_account}</h2>
            {if !$customer_id}
            <label>
                   <div class="float-left login_input"><input type='radio' name='account_option' value="{if $customer_id}new{else}exists{/if}" /></div> 
                   <div class="float-left login_text"><span class="green_text">{$lng.lbl_returning_customer}</span><br /><span style="font-weight: normal;">{$lng.lbl_returning_customer_txt}</span></div>

            </label>
            <br />
            <label>
                <div class="float-left login_input"><input type='radio' name='account_option' value="new" /> </div>
                <div class="float-left login_text">
                   <span class="green_text">{$lng.lbl_new_customer}</span><br />
                   <span style="font-weight: normal;">{$lng.lbl_new_customer_txt}</span>
                </div>
            </label>
            {/if}
          </div>
        </div>
    <!-- cw@checkout_enter ] -->

        <div class="float-right width50">
          <div class="checkout_block">
            <h2>{$lng.lbl_already_registered}</h2>
            {include file='customer/checkout/enter.tpl'}
          </div>
        </div>
        </div>
        </div>
    </li>
           

    <li id="onestep_login" class="opc-section">{include file='customer/checkout/create.tpl'}</li>

    <!-- DIV container for 3 li sections: shipping, payment, summary -->
    <li id="onestep_method" class="opc-section">
        {include file='customer/checkout/method.tpl'}
    </li>
    <!-- / DIV container for 3 sections -->

    <li id="onestep_place" class="opc-section opc-visibility-section">{include file='customer/checkout/place.tpl'}</li>

</ul>
</div>
