{capture name=block}






<div style="margin-left: 20%;">
<div style="float:left; width:450px;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_new_message}</h3></div>
<div class="col-sm-12" style="padding:10px 0 10px 15px;">{$lng.lbl_new_message_note}</div>
<div style="width:450px;" class="jasellerblock-content">


<form method="post" name="new_message_form" action="index.php?target={$current_target}" class="form-horizontal">
    <input type="hidden" name="action" value="new_message">
    <input type="hidden" name="new_message[recipient_id]" value="{$recipient_id}">
    <input type="hidden" name="new_message[conversation_id]" value="{$conversation_id}">

    <div class="form-group">
        <label class="col-xs-12">{$lng.lbl_sender}</label>
    	<div class="col-xs-12">
        	<input type="text" class="form-control" name="new_message[sender_name]" value="{$sender_name}" size="30">
        </div>
    </div>

    <div class="form-group">
        <label class="required col-xs-12">{$lng.lbl_recipient}</label>
        <div class="col-xs-12">
        	<input type="text" class="form-control" id="new_message_recipient_name" name="new_message[recipient_name]" value="{$recipient_name}" size="30">
		</div>
    </div>

    <div class="form-group">
        <label class="required col-xs-12">{$lng.lbl_subject}</label>
        <div class="col-xs-12">
        	<input type="text" style="width: 350px; height: auto;" class="form-control" id="new_message_subject" name="new_message[subject]" value="{$subject}" rows="10" size="50" maxlength="255">
		</div>
    </div>

    <div class="form-group">
        <label class="col-xs-12">{$lng.lbl_body}</label>
        <div class="col-xs-12">
        	<textarea style="width: 350px; height: 150px;" class="form-control" name="new_message[body]">{$body}</textarea>
        </div>
    </div>

    <div class="buttons">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_send href="javascript: void(0);" onclick="check_required_fields_and_submit()" style="btn-green push-20"}</div>
</form>

</div></div></div>
<div style="margin-left: 500px;"><img src="/cw/images/Avatar_Seller_New_Message.gif" width="135" height="398"></div>
<div style="clear: both;"></div>

<script type="text/javascript">
var lbl_please_fill_required_fields = "{$lng.lbl_please_fill_required_fields}";
var txt_recipient_not_found = "{$lng.txt_recipient_not_found}";
var current_url = "index.php?target={$current_target}";
{literal}
    function check_required_fields_and_submit() {

        if (
            $('#new_message_recipient_name').val() == ""
            || $('#new_message_subject').val() == ""
        ) {
            alert(lbl_please_fill_required_fields);

            if ($('#new_message_recipient_name').val() == "") {
                $('#new_message_recipient_name').focus();
            }
            else {
                $('#new_message_subject').focus();
            }

            return false;
        }

        // check recipient email
        var value = $('#new_message_recipient_name').val();
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (
            regex.test(value)
            || (
                $.isNumeric(value)
                && value > 0
            )
        ) {
            $.ajax({
                type: 'post',
                url: current_url,
                data: 'check_recipient_value=1&is_ajax=1&test_value=' + value,
                success: function(data) {
                    if (data == 'avail') {
                        cw_submit_form('new_message_form')
                    }
                    else {
                        alert(txt_recipient_not_found);
                        return false;
                    }
                },
                error: function() {
                    alert('Error occured (debug: JS ajaxPost)');
                    return false;
                }
            });
        }
        else {
            cw_submit_form('new_message_form');
        }
    }
{/literal}
</script>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}
