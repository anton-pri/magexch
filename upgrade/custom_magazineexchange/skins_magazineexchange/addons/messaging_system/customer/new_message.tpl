<div class="new_message_undertitle">
{$lng.lbl_new_message_note2seller}
</div>
<form method="post" name="new_message_form" action="index.php?target={$current_target}">
    <input type="hidden" name="action" value="new_message">
    <input type="hidden" name="new_message[recipient_id]" value="{$recipient_id}">
    <input type="hidden" name="new_message[conversation_id]" value="{$conversation_id}">

    <div class="new_message_fields">
      <div class="field">
        <div class="field_title">
            {$lng.lbl_sender}
        </div> 
        <div class="field_input"> 
            <input type="text" name="new_message[sender_name]" value="{$sender_name}" size="30">
        </div>
      </div> 

      <div class="field">
        <div class="field_title">
            <label class="required"><b>{$lng.lbl_recipient}</b></label>
        </div> 
        <div class="field_input">
            <input type="text" id="new_message_recipient_name" name="new_message[recipient_name]" value="{$recipient_name}" size="30">
        </div>
      </div> 

      <div class="field">
        <div class="field_title">
            <label class="required"><b>{$lng.lbl_subject}</b></label>
        </div> 
        <div class="field_input long">
            <input type="text" id="new_message_subject" name="new_message[subject]" value="{$subject}" size="60" maxlength="255">
        </div> 
      </div>

      <div class="field">
        <div class="field_title">
            {$lng.lbl_body}
        </div> 
        <div class="field_input long">
            <textarea name="new_message[body]">{$body}</textarea>
        </div>  
      </div>
      <div class="buttons"> 
          {include file='buttons/button.tpl' button_title=$lng.lbl_send href="javascript: void(0);" onclick="check_required_fields_and_submit()" style="button" class="new_message_send"}
      </div> 
    </div>
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
