<section>
{if $mode eq 'sent'}
    {*include file='common/page_title.tpl' title=$lng.lbl_sent*}
    {capture name=section}
    <div id="contents_messages_list" blockUI="contents_messages_list">
        {include file="addons/messaging_system/admin/messages.tpl"}
    </div>
    {/capture}
    {include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_sent}
{elseif $mode eq 'archive'}
    {*include file='common/page_title.tpl' title=$lng.lbl_archive*}
    {capture name=section}
    <div id="contents_messages_list" blockUI="contents_messages_list">
        {include file="addons/messaging_system/admin/messages.tpl"}
    </div>
    {/capture}
    {include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_archive}
{elseif $mode eq 'new'}
    {capture name=section}
    <div class="box">
        {*include file='common/subheader.tpl' title=$lng.lbl_new_message*}
        {include file="addons/messaging_system/admin/new_message.tpl"}
    </div>
    {/capture}
    {include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_new_message}
{elseif $mode eq 'show'}
    {*include file='common/page_title.tpl' title=$message.subject*}
    {capture name=section}
    {include file="addons/messaging_system/admin/show_message.tpl"}
    {/capture}
    {include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$message.subject}
{else}
    {*include file='common/page_title.tpl' title=$lng.lbl_avail_type_incoming*}
    {capture name=section}
    <div id="contents_messages_list" blockUI="contents_messages_list">
        {include file="addons/messaging_system/admin/messages.tpl"}
    </div>
    {/capture}
    {include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_avail_type_incoming}

{/if}
</section>

<script type="text/javascript">
    <!--
    var new_message = "{$messages_counter.new}";
    var incoming_message = "{$messages_counter.incoming}";
    var sent_message = "{$messages_counter.sent}";
    var archive_message = "{$messages_counter.archive}";
    {literal}
    $(document).ready(function() {
        var incoming_text = "(" + incoming_message + ")";
        if (new_message > 0) {
            incoming_text = "<b>" + new_message + "</b>" + incoming_text;
        }
        var el = $('.tabs a[href="index.php?target=message_box"]').find('span');
        var incoming_html = el.html();
        el.html(incoming_html + " " + incoming_text);

        var sent_text = " (" + sent_message + ")";
        var el = $('.tabs a[href="index.php?target=message_box&mode=sent"]').find('span');
        var sent_html = el.html();
        el.html(sent_html + sent_text);

        var archive_text = " (" + archive_message + ")";
        var el = $('.tabs a[href="index.php?target=message_box&mode=archive"]').find('span');
        var archive_html = el.html();
        el.html(archive_html + archive_text);
    });
    {/literal}
    -->
</script>
