<script type="text/javascript">
var previous_tabs = new Array();
var tabs_images_dir='{$ImagesDir}/tab/';
{literal}

function switchOn(tab, contents, tab_name, group) {
    clearContents(group);
    previous_tab = previous_tabs[group];
	if (isset(previous_tab)) {
        document.getElementById(previous_tab).className = 'section_tab';
    }
	previous_tabs[group] = tab;

    if (!document.getElementById(tab)) return;

    document.getElementById(tab).className = 'section_tab_selected';
    if (document.getElementById(contents))
        document.getElementById(contents).className = 'tab_content_selected';

    el = document.getElementById('form_js_tab');
    if (el) el.value = tab_name;

    $('body').trigger('switch_to_tab', [tab, contents, tab_name]);
}

function clearContents(group) {
    contentsCell = document.getElementById('contentscell'+group);
    contentsArray = contentsCell.childNodes;
    for (var j=0; j<contentsArray.length; j++)
        contentsArray[j].className = 'tab_content_not_selected';
}
</script>
{/literal}
