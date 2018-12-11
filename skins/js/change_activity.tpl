<script type="text/javascript">
var lbl_please_select = "{$lng.lbl_please_select}";
{literal}
function handler_activities_list(data) {
    select = document.getElementById('{/literal}{$name|id}{literal}');
    while (select.options.length > 0)
        select.options[select.options.length-1] = null;
    select.options[select.options.length] = new Option(lbl_please_select, 0);
    sel_index = 0;
    index = 1;
    if (data.activities)
    for (i in data.activities) {
        select.options[select.options.length] = new Option(data.activities[i].title, i);
        if (i == data.selected) sel_index = index;
        index++;
    }
    select.selectedIndex = sel_index;
}

function ajax_update_activities_list() {
    $.ajax({
    "url":{/literal}'index.php?target={$current_target}&mode=activities&user={$user}&action=ajax_update'{literal},
    "success":handler_activities_list,
    "dataType":"json",
    "type":"post",
    });
}
{/literal}
</script>
