<form method="post" name="new_message_form" action="index.php?target={$current_target}">
    <input type="hidden" name="action" value="new_message">
    <input type="hidden" name="new_message[recipient_id]" value="{$recipient_id}">
    <input type="hidden" name="new_message[conversation_id]" value="{$conversation_id}">

    <table width="100%" class="new_message_table">
        <tr>
            <td width="70px"><b>{$lng.lbl_sender}</b></td>
            <td><input type="text" name="new_message[sender_name]" value="{$sender_name}" size="30"></td>
        </tr>
        <tr>
            <td><label class="required"><b>{$lng.lbl_recipient}</b></label></td>
            <td><input type="text" id="new_message_recipient_name" name="new_message[recipient_name]" value="{$recipient_name}" size="30"></td>
        </tr>
        <tr>
            <td><label class="required"><b>{$lng.lbl_subject}</b></label></td>
            <td><input type="text" id="new_message_subject" name="new_message[subject]" value="{$subject}" size="60" maxlength="255"></td>
        </tr>
        <tr>
            <td valign="top"><b>{$lng.lbl_body}</b></td>
            <td><textarea style="width: 400px; height: 100px;" name="new_message[body]">{$body}</textarea></td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                {include file='buttons/button.tpl' button_title=$lng.lbl_send href="javascript: void(0);" onclick="check_required_fields_and_submit()" style="button"}
            </td>
        </tr>
    </table>
</form>

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