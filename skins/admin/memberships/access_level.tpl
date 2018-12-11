{capture name="section"}
{literal}
<script language="Javascript">

function is_ch(key) {
    ret = false;
    for(i = 0 ; i < document.ch_form.length; i++) {
        if (document.ch_form[i].id.substr(0, key.length) == key && document.ch_form[i].checked && key != document.ch_form[i].id) ret = true;
    }
    return ret;
}

function is_same(key) {
    ret = true;
    for(i = 0 ; i < document.ch_form.length; i++) {
        nm = document.ch_form[i];
        if (nm.id.length == key.length && nm.checked && key != nm.id && nm.id.substr(0, nm.id.length-2) == key.substr(0, key.length-2)) ret = false;
    }
    return ret;
}

function authm(key) {
    el = document.getElementById(key);

    elm = document.getElementById('__'+key);
    if (elm) elm.disabled = !el.checked;


    tmp =  key.substr(0, key.length-2);
    flag = is_ch(key);

//group child check/uncheck
    if ((flag && !el.checked) || (!flag && el.checked))
    for(i = 0 ; i < document.ch_form.length; i++) {
        tmp_ = document.ch_form[i].id.substr(0, key.length);
        if (document.ch_form[i].id.length > key.length && key == tmp_ && !document.ch_form[i].disabled) {
            document.ch_form[i].checked = el.checked;
            elm = document.getElementById('__'+document.ch_form[i].id);
            if (elm) elm.disabled = !el.checked;
        }
    }
   
// parent check/uncheck 
    flag = is_same(key);
    tmp = key;
    while (tmp.length && flag) {
        tmp = tmp.substr(0, tmp.length-2);
        el1 = document.getElementById(tmp);
        if (el1 && !el1.disabled) {
            el1.checked = el.checked;
            elm = document.getElementById('__'+el1.id);
            if (elm) elm.disabled = !el.checked;
        }
    }
}
</script>
{/literal}

{capture name="block"}
<div class="box">

<form name="ch_form" method="POST" action="index.php?target={$current_target}">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="mem_area" value="{$mem_area}" />
<input type="hidden" name="membership_id" value="{$membership_id}" />

{if $def}
<table width="100%" class="table table-striped dataTable vertical-center">
<thead>
<tr>
    <th width="98%" nowrap style="text-align: left;">{$lng.lbl_page}</th>
    <th width="1%">{$lng.lbl_read}</th> 
    <th width="1%">{$lng.lbl_modify}</th>
</tr>
</thead>
{include file='admin/memberships/access_level_ex.tpl' level=0}
</table>
{/if}

</form>

</div>

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_apply href="javascript:cw_submit_form('ch_form');" style="btn-green push-20"}
{/capture}
{include file='admin/wrappers/block.tpl' content=$smarty.capture.block}
{/capture}
{include file='admin/wrappers/section.tpl' content=$smarty.capture.section title=$lng.lbl_access_level}