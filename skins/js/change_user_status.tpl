<script language="javascript">
// TODO: This code is not used anymore. Remove this file
{literal}
function cw_change_user_status(status, note_id) {
    note_el = document.getElementById(note_id);
    if (!note_el) return;
    if (status != 'Y') note_el.style.display = '';
    else note_el.style.display = 'none';
}
{/literal}
</script>
