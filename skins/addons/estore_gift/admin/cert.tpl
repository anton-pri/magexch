{include_once file='js/check_email_script.tpl'}
{include_once file='main/include_js.tpl' src='js/register.js'}
{include_once_src file='main/include_js.tpl' src='js/popup_user.js'}
<script type="text/javascript">
    <!--
    var txt_recipient_invalid 		= "{$lng.txt_recipient_invalid|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
    var txt_amount_invalid 			= "{$lng.txt_amount_invalid|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
    var txt_gc_enter_mail_address 	= "{$lng.txt_gc_enter_mail_address|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";

    var orig_mode 				= "{$mode|escape:"javascript"}";
    var min_gc_amount 			= {$min_gc_amount|default:0};
    var max_gc_amount 			= {$max_gc_amount|default:0};
    var is_c_area 				= false;
    var enablePostMailGC 		= "{$config.estore_gift.enablePostMailGC}";
    var required_field_empty 	= "{$lng.lbl_required_field_is_empty|strip_tags|escape:javascript}";
    var lbl_gift_certificate 	= "{$lng.lbl_gift_certificate}";

    {literal}
    var gift_certificate_field_empty = substitute(required_field_empty, 'field', lbl_gift_certificate);


    function fill_gc_form(user_id, user_role){

        $.ajax({
            type: 'get',
            url:  'index.php?target=giftcert_user_data',
            data: 'user_id='+user_id,
            dataType: 'json',
            success: function(data) {
                if(data){
                    if(user_role=='purchaser'){
                        document.gccreate.purchaser.value = data.firstname + ' ' +data.lastname;
                    }else if(user_role=='recipient'){
                        document.gccreate.recipient_to.value = data.firstname + ' ' +data.lastname;
                        document.gccreate.recipient_email.value = data.email;
                        document.gccreate.recipient_firstname.value = data.firstname;
                        document.gccreate.recipient_lastname.value = data.lastname;
                        document.gccreate.recipient_address.value = data.address;
                        document.gccreate.recipient_city.value = data.city;
                        document.gccreate["recipient[state]"].value = data.state;
                        document.gccreate["recipient[country]"].value = data.country;
                        document.gccreate.recipient_zipcode.value = data.zipcode;
                    }
                }
            },
            error: function() {alert('Error occured (debug: JS fill_gc_form)');  },
            complete:function(){            $('input[name="purchaser_id"], input[name="recipient_to_id"]').val('');}
        })


    }


    function check_gc_form() {

        if(document.gccreate.purchaser_id.value !=0){
            fill_gc_form(document.gccreate.purchaser_id.value, 'purchaser');
            return false;
        }
        if(document.gccreate.recipient_to_id.value !=0){
            fill_gc_form(document.gccreate.recipient_to_id.value, 'recipient');
            return false;
        }

        if (document.gccreate.purchaser.value == "") {
            document.gccreate.purchaser.focus();
            alert(txt_gc_enter_mail_address);
            return false;
        }

        if (document.gccreate.recipient_to.value == "") {
            document.gccreate.recipient_to.focus();
            alert(txt_recipient_invalid);
            return false;
        }

        var num = convert_number(document.gccreate.amount.value);

        if (
                !check_is_number(document.gccreate.amount.value)
                        || (is_c_area && (num < min_gc_amount || (max_gc_amount > 0 && num > max_gc_amount)))
                ) {
            document.gccreate.amount.focus();
            alert(txt_amount_invalid);
            return false;
        }

        if (enablePostMailGC == 'Y') {

            if (
                    document.gccreate.send_via[0].checked
                            && !checkEmailAddress(document.gccreate.recipient_email)
                    ) {
                document.gccreate.recipient_email.focus();
                return false;
            }

            if (
                    document.gccreate.send_via[1].checked
                            && (
                            document.gccreate.recipient_firstname.value == ""
                                    || document.gccreate.recipient_lastname.value == ""
                                    || document.gccreate.recipient_address.value == ""
                                    || document.gccreate.recipient_city.value == ""
                                    || document.gccreate["recipient[state]"].value == ""
                                    || document.gccreate.recipient_zipcode.value == ""
                            )
                    ) {
                document.gccreate.recipient_firstname.focus();
                alert(txt_gc_enter_mail_address);
                return false;
            }

        }
        else if (!checkEmailAddress(document.gccreate.recipient_email)) {
            document.gccreate.recipient_email.focus();
            return false;
        }

        return true;
    }

    function formSubmit() {
        if (check_gc_form()) {
            document.gccreate.action.value = orig_mode;
            document.gccreate.mode.value = orig_mode;
            document.gccreate.target = ''
            cw_submit_form(document.gccreate);
        }
    }
    -->
</script>
{/literal}

{if $config.estore_gift.enablePostMailGC eq "Y"}
    <script type="text/javascript" language="JavaScript 1.2">
        <!--
{literal}
$(document).ready(function() {
	switchPreview();
});

function switchPreview() {
	if (document.gccreate.send_via[0].checked) {
		document.getElementById('preview_button').style.display='none';
	    document.getElementById('preview_template').style.display='none';
	}
	if (document.gccreate.send_via[1].checked) {
		document.getElementById('preview_button').style.display='';
	    document.getElementById('preview_template').style.display='';
	}
}

function formPreview() {
	if (check_gc_form()) {
		document.gccreate.action.value='preview';
		document.gccreate.mode.value='preview';
		document.gccreate.target='_blank'
		cw_submit_form(document.gccreate);
	}
}
{/literal}
-->
    </script>
{else}
    <script type="text/javascript" language="JavaScript 1.2">
        <!--
{literal}
function switchPreview() {
	return false;
}
{/literal}
-->
    </script>
{/if}

{include file='js/check_zipcode_js.tpl'}

{*include file='common/page_title.tpl' title=$lng.lbl_gift_certificate*}
{capture name=section}
{capture name=block}


<form name="gccreate" action="index.php?target=giftcerts" method="post" onsubmit="javascript: return check_gc_form()" class="form-horizontal">
    <input type="hidden" name="action" value="{$mode|escape:"html"}" />
    <input type="hidden" name="gc_id" value="{$gc_id|escape:"html"}" />
    <input type="hidden" name='mode' value='' />
    <div class="box">
        <div class="form-group">
            <label class="col-xs-12">1. {$lng.lbl_gc_whom_sending}</label>
            <div class="col-xs-12">{$lng.lbl_gc_whom_sending_subtitle}</div>
        </div>
        <div class="form-group">
            <label class="required col-xs-12">{$lng.lbl_from}</label>
            <div class="col-xs-12 form-inline">
            	<div class="form-group">
            		<input type="text" class="form-control" name="purchaser" id="purchaser" size="30" value="{if $mode eq 'add_gc'}{$config.Company.company_name}{else}{if $giftcert.purchaser}{$giftcert.purchaser|escape:"html"}{/if}{/if}" />
            		<input type="hidden"   id="purchaser_id" name="purchaser_id" value=""  />
            	</div>
            	<div class="form-group">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_select_from_existing onclick="javascript: cw_popup_user('gccreate', 'purchaser_id', 'C')" style="btn-green"}</div>
			</div>
        </div>
        <div class="form-group">
            <label class="required col-xs-12">{$lng.lbl_to}</label>
            <div class="col-xs-12 form-inline">
            	<div class="form-group">
            		<input type="text" class="form-control" name="recipient_to" id="recipient_to" size="30" value="{$giftcert.recipient|escape:"html"}" />
            		<input type="hidden"   id="recipient_to_id" name="recipient_to_id" value=""  />
            	</div>
            	<div class="form-group">
            		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_select_from_existing onclick="javascript: cw_popup_user('gccreate', 'recipient_to_id', 'C')" style="btn-green"}
				</div>
			</div>
        </div>
        <div class="form-group">
            <label class="col-xs-12">2. {$lng.lbl_gc_add_message}</label>
            <div class="col-xs-12">
            	{$lng.lbl_gc_add_message_subtitle}
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12">{$lng.lbl_message}</label>
            <div class="col-xs-12">
            	<textarea name="message" class="form-control" rows="8" cols="50">{$giftcert.message}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="required col-xs-12">3. {$lng.lbl_gc_choose_amount}</label>
            <div class="col-xs-12"><p>{$lng.lbl_gc_choose_amount_subtitle}</p></div>
            <div class="col-xs-12 form-inline">
            	<div class="form-group">{$config.General.currency_symbol}</div>
            	<div class="form-group"><input type="text" class="form-control" name="amount" size="10" maxlength="9" value="{$giftcert.amount|formatprice}" /></div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12">4. {$lng.lbl_gc_choose_delivery_method}</label>
        </div>

        <div class="form-group">
            <label class="col-xs-12">
                {if $config.estore_gift.enablePostMailGC eq "Y"}
                    <input id="gc_send_e" type="radio" name="send_via" value="E" onclick="switchPreview();"{if $giftcert.send_via ne "P"} checked="checked"{/if} />
                {else}
                    <input type="hidden" name="send_via" value="E" />
                {/if}
                {$lng.lbl_gc_send_via_email}
            </label>
        </div>
        <div class="form-group">
            <label class="required col-xs-12">{$lng.lbl_gc_enter_email}</label>
            <div class="col-xs-12"><input type="text" class="form-control" name="recipient_email" size="30" value="{$giftcert.recipient_email}" /></div>
        </div>

        {if $config.estore_gift.enablePostMailGC eq "Y"}
            <div class="form-group">
                <label class="col-xs-12">
                    <input id="gc_send_p" type="radio" name="send_via" value="P" onclick="switchPreview();"{if $giftcert.send_via eq "P"} checked="checked"{/if} /></td>
                    {$lng.lbl_gc_send_via_postal_mail}
                </label>
            </div>

            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_firstname}</label>
                <div class="col-xs-12"><input type="text" class="form-control" name="recipient_firstname" size="30" value="{$giftcert.recipient_firstname}" /></div>
            </div>
            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_lastname}</label>
                <div class="col-xs-12"><input type="text" class="form-control" name="recipient_lastname" size="30" value="{$giftcert.recipient_lastname}" /></div>
            </div>
            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_address}</label>
                <div class="col-xs-12"><input type="text" class="form-control" name="recipient_address" size="40" value="{$giftcert.recipient_address}" /></div>
            </div>
            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_city}</label>
                <div class="col-xs-12"><input type="text" class="form-control" name="recipient_city" size="30" value="{$giftcert.recipient_city}" /></div>
            </div>
            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_state}</label>
                <div class="col-xs-12">{include file='main/map/_states.tpl' name="recipient[state]" id="recipient[state]" default=$giftcert.recipient_state}</div>
            </div>
            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_country}</label>
                <div class="col-xs-12">
                	<select id="recipient_country" class="form-control" name="recipient[country]" size="1" onchange="cw_address_init(this.value, '', 'recipient[country]')">
                    {section name=country_idx loop=$countries}
                        <option value="{$countries[country_idx].country_code}"{if $giftcert.recipient_country eq $countries[country_idx].country_code} selected="selected"{elseif $countries[country_idx].country_code eq $config.General.default_country and $giftcert.recipient_country eq ""} selected="selected"{elseif $countries[country_idx].country_code eq $userinfo.country && $giftcert.recipient_country eq ""} selected="selected"{/if}>{$countries[country_idx].country}</option>
                    {/section}
                	</select>
                </div>
            </div>

            <div class="form-group">
                <label class="required col-xs-12">{$lng.lbl_zipcode}</label>
                <div class="col-xs-12"><input type="text" class="form-control" name="recipient_zipcode" size="30" value="{$giftcert.recipient_zipcode}" onchange="javascript: check_zip_code_field(document.forms['gccreate'].recipient.country, document.forms['gccreate'].recipient_zipcode);" /></div>
            </div>
            <div class="form-group">
                <label class="col-xs-12">{$lng.lbl_phone}</label>
                <div class="col-xs-12"><input type="text" class="form-control" name="recipient_phone" size="30" value="{$giftcert.recipient_phone}" /></div>
            </div>

            <div class="form-group" id="preview_template">
                <label class="col-xs-12">{$lng.lbl_gc_template}</label>
                <div class="col-xs-12">
                <select name="gc_template" class="form-control">
                    {foreach from=$gc_templates item=gc_tpl}
                        <option value="{$gc_tpl|escape}"{if $gc_tpl eq $giftcert.tpl_file or $giftcert.tpl_file eq "" and $gc_tpl eq $config.estore_gift.default_giftcert_template} selected="selected"{/if}>{$gc_tpl}</option>
                    {/foreach}
                </select>
                </div>
            </div>

        {/if}

    </div>
</form>
<div class="buttons">
    <div id="preview_button">
        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_preview href="javascript: void(formPreview());" style='btn-green push-20 push-5-r'}
    </div>

    {if $mode eq "modify_gc"}
        {include file="admin/buttons/gc_update.tpl" href="javascript: formSubmit();" style='btn-green push-20 push-5-r'}
    {else}
        {include file='admin/buttons/button.tpl' button_title=$lng.lbl_gc_create href="javascript: formSubmit();" style='btn-green push-20 push-5-r'}
    {/if}
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_gift_certificate}
