{literal}
<style>
.selectBox-tiny {
	width: 45px;
        padding: 1px!important;
        font-size: 10px;
	line-height: 2.3;
}

.selectBox-middle {
	width: 150px;
        padding: 1px!important;
        font-size: 10px;
	line-height: 2.3;
}

.SB-addtocat {
	width: auto!important;
}

.button-left {
	padding: 5px 12px;
	text-align: center;	
}

.msgbx {
        padding:5px;
        border: 1px solid #81351A;
        color: #81351A;
        margin: 8px 0;
}

</style>



<script type="text/javascript">

{/literal}
var ic_save='{$ic_save}'; var ic_edit='{$ic_edit}'; var ic_del='{$ic_del}';
var ic_coll='{$ic_coll}'; var ic_expd='{$ic_expd}';
{literal}



var update=[]; var removed=[];
var update_arr=[]; var children=[];
var check_added=0; var addn_last=1;
var edited_rows=[];

function expand (id,lev) {

var chld=get_childs_of(id);
var chld=chld.split(',');
if (document.getElementById(chld[0]) == null) {
  ajaxGet('index.php?target=top_menu&get_menu_subitems='+id+'&level='+lev, null);
}

for (var i=0;i<chld.length; i++) {
  if (document.getElementById(chld[i]) != null) {
    document.getElementById(chld[i]).style.display='';
  }
}
var icon=document.getElementById('exp-coll-'+id);
icon.src=ic_coll;
icon.setAttribute ('onclick', "collapse('"+id+"');");

}

function al (ajax_id) {

  alert(ajax_id);
}
function checkbox_statuses (id) {
  var active=document.getElementById('active-'+id).checked;
  if (active) blockchildren(id,false); else blockchildren(id,true);
}

function collapse (id) {
    var chld=children[id].split(',');
      for (var i=0;i<chld.length; i++) {
        //alert (chld[i]);
        var icon=document.getElementById('exp-coll-'+chld[i]);
        if (icon!=undefined) {
          icon.src=ic_expd;
          icon.setAttribute ('onclick', "expand('"+chld[i]+"');");
          collapse(chld[i]);

      }
      if (document.getElementById(chld[i]) != null) {
        document.getElementById(chld[i]).style.display='none';
      }
    }
  var icon=document.getElementById('exp-coll-'+id);
  icon.src=ic_expd;
  icon.setAttribute ('onclick', "expand('"+id+"');");
}

function expand_all () {
var chld=get_childs_of(0);
var chld=chld.split(',');
for (var i=0;i<chld.length; i++) if (children[chld[i]]!=undefined) expand(chld[i]);
}

function collapse_all () {
var chld=get_childs_of(0);
var chld=chld.split(',');
for (var i=0;i<chld.length; i++) if (children[chld[i]]!=undefined) collapse(chld[i]);
}


function blockchildren(mid,bl) {
	if (children[mid] != null) {
		var chld=children[mid].split(',');

		for (var i=0;i<chld.length; i++) {
			var c=chld[i];
                     if (document.getElementById('active-'+c) != null) {
			    document.getElementById('active-'+c).disabled=bl;
                     }
		}

		if (!bl) {
			for (i=0;i<chld.length; i++) {
				c=chld[i];
				if (children[c] != null) {
					var active=document.getElementById('active-'+c).checked;
					if (!active) blockchildren(c,true);
				}
			}
		}

	} 
}


function get_path_name (mid) {
if (mid=='0') var ret='Top level';
else {
var path=document.getElementById('title-path-'+mid).innerHTML;
var tit=document.getElementById('title-name-ed-'+mid).value;
var ret=path+tit;
}
return ret; 
}

function checked_to_drop (id) {
if (document.getElementById('drop-cb-'+id)!=undefined)
return document.getElementById('drop-cb-'+id).checked;
else return false;
}

function get_childs_of_nd (mid) {
var list=''; var id=''; var pid;
var els=document.getElementById('trows').childNodes;
for (var i in els) if (els[i].id!=undefined & els[i].id!='' && !checked_to_drop(els[i].id)) {
id=els[i].id;
if (document.getElementById('item-perent-'+id) != null) {
  pid=document.getElementById('item-perent-'+id).value;
}
if (pid==mid) if (list!='') list+=','+id; else list=id;
}
return list;
}

function get_childs_of (mid) {
var list=''; var id=''; var pid;
var els=document.getElementById('trows').childNodes;
for (var i in els) if (els[i].id!=undefined & els[i].id!='') {
id=els[i].id;
if (document.getElementById('item-perent-'+id) != null) {
  pid=document.getElementById('item-perent-'+id).value;
}
if (pid==mid) if (list!='') list+=','+id; else list=id;
}
return list;
}



function get_prnts_of (mid) {
if (checked_to_drop(mid)) return '0';
if (mid=='0') return '';
var pid=document.getElementById('item-perent-'+mid).value;
if (pid=='0' || checked_to_drop(pid)) return '0';
return get_prnts_of(pid)+','+pid;
}

function prnt_child_chain_of (mid) {
if (checked_to_drop(mid)) mid='0';
var list1=get_prnts_of (mid);
var list2=get_childs_of_nd (mid);
if (list1=='' && list2=='') list=mid;
else if (list1=='') list=mid+','+list2;
else if (list2=='') list=list1+','+mid;
else list=list1+','+mid+','+list2;
return list;
}



function cat_list_select (id,num) {
var list=prnt_child_chain_of (id);
var arr=list.split(',');
var ret=[];

for (i in arr) {
ret[arr[i]]=get_path_name(arr[i]);
}

var cont=document.getElementById('a'+num+'-cont-sel');
while (cont.childNodes.length >= 1) cont.removeChild(cont.firstChild); 
var sel=document.createElement("select");
sel.setAttribute ('onchange',"cat_list_select(this.value,"+num+");");
sel.setAttribute ('class',"SB-addtocat form-control");
sel.setAttribute ('id',"a"+num+"-addto");

for (i in ret) {
var newopt = new Option(ret[i],i);
if (i==id) newopt.selected=true;
sel.options[sel.options.length]=newopt;
}
cont.appendChild(sel);
//alert (cont.innerHTML);
}

function add_new_item (id) {
cat_list_select (id,1);
document.getElementById('addnewitem').style.display='block';
document.getElementById('a1').style.display='';
check_added=1;
preview_changes ();
}

function anew_show_next () {
var max=15; var addnlast=++addn_last;
if (addnlast<=max) {
document.getElementById('a'+addnlast).style.display='';
cat_list_select (document.getElementById("a"+(addnlast-1)+"-addto").value,addnlast);
if (addnlast==max) document.getElementById('anew').style.display='none';
} 
}

function make_new_item_post_line () {
var ret='';
for (var i=1; i<=addn_last; i++) {
var title=document.getElementById('a'+i+'-title').value;
var link=document.getElementById('a'+i+'-link').value;
var pos=document.getElementById('a'+i+'-pos').value;
var active=document.getElementById('a'+i+'-active').checked;
if (active) var active=1; else var active=0;
var perent=document.getElementById('a'+i+'-addto').value;
title=title.replace(/(^\s+)|(\s+$)/g, "");
link=link.replace(/(^\s+)|(\s+$)/g, "");
if (title!='') ret+='add'+'---'+perent+'---'+pos+'---'+active+'---'+title+'---'+title+'---'+link+"\n";
}
return ret;
}

function show_btns () {
	if (obj_length(update)!=0 || obj_length(removed)!=0) {
		document.getElementById('btns1').style.display='block';
		document.getElementById('before_btns1').style.display='none';
	}
	else {
		document.getElementById('btns1').style.display='none';
		document.getElementById('before_btns1').style.display='block';
	}
}

function log_updates (mid) {
	var pos=document.getElementById('pos-'+mid).value;
	var active=document.getElementById('active-'+mid).checked;
	var itype=document.getElementById('item-type-'+mid).value;
	var tit=document.getElementById('title-name-ed-'+mid).value;
	var ulink='0';
	if (itype=='ucat') ulink=document.getElementById('title-link-ed-'+mid).value;
	var orig=document.getElementById('title-name-orig-'+mid).value;

	update[mid]={id:mid,p:pos,a:active,t:tit,o:orig,l:ulink};
	show_btns();
	if (!active) blockchildren(mid,true); else blockchildren(mid,false);

}



function on_drop_item_chng (id,sndr) {
	if (sndr.checked) { var rmids=id; var dis='none'; } else { var rmids=undefined; var dis='inline'; }
	document.getElementById('addnew-'+id).style.display=dis;
	if (children[id]!=undefined) {
		if (rmids!=undefined) rmids+=','+children[id];
		var cldn=children[id].split(',');
		for (var i in cldn) {
			var cb=document.getElementById('drop-cb-'+cldn[i]);
			cb.checked=sndr.checked;
			cb.disabled=sndr.checked;
			document.getElementById('addnew-'+cldn[i]).style.display=dis;
			if (removed[cldn[i]]!=undefined) delete removed[cldn[i]];
		}
	}
if (sndr.checked) removed[id]=rmids; else if (removed[id]!=undefined) delete removed[id];
show_btns();
//alert(removed[id]);
}

function restore_title (id,sndr) {
var orig=document.getElementById('title-name-orig-'+id).value;
document.getElementById('title-name-old-'+id).value=document.getElementById('title-name-ed-'+id).value;
document.getElementById('title-name-ed-'+id).value=orig;
document.getElementById('title-name-'+id).innerHTML=orig;
sndr.style.display='none';
log_updates (id);
}

function obj_length (obj) {
l=0; for (var i in obj) l++; return l;
}

function adds_empty () {
var cnt=0; for (var i=1; i<=addn_last; i++) if (document.getElementById('a'+i).style.display!='none') cnt++;
if (document.getElementById('anew').style.display!='none') cnt++;
if (cnt==0) return true; else return false;
}

function reload_if_all_erased () {
if (document.getElementById('addnewitem').style.display=='none' && obj_length(update_arr)==0) {
document.getElementById('btns2').style.display='none';
document.getElementById('nothingtosubmit').style.display='block';
refuse_changes ();
}
}

function erase_removed (id) {
delete update_arr[id];
var cb=document.getElementById('drop-cb-'+id);
cb.checked=false;
on_drop_item_chng(id,cb);
for (var i=1; i<=addn_last; i++) {
var asel=document.getElementById("a"+i+"-addto");
if (asel!=undefined) cat_list_select (asel.value,i);
}
document.getElementById('rem-'+id).style.display='none';
if (obj_length(removed)==0) document.getElementById('removed').style.display='none';
reload_if_all_erased();
//alert (obj_length(removed));
}

function erase_update (id) {
delete update_arr[id];
document.getElementById('up-'+id).style.display='none';
if (obj_length(update_arr)==obj_length(removed)) document.getElementById('changes').style.display='none';
reload_if_all_erased();
//alert (obj_length(update_arr)+','+obj_length(removed));
}

function erase_added (id) {
document.getElementById('a'+id+'-title').value='';
document.getElementById('a'+id+'-link').value='';
document.getElementById('a'+id).style.display='none';
if (adds_empty()) document.getElementById('addnewitem').style.display='none';
reload_if_all_erased();
}


function pre_update (prev) {
	for (var i in edited_rows) {
		accept_row (i,edited_rows[i]);
	}

	for (var i in removed) {
		var rmids=removed[i].split(',');
		for (var j in rmids) {
			delete update[rmids[j]];
		}
		update_arr[i]='remove---('+removed[i]+')---0---0---0---0---0';
		if (prev==1) {
			var mid=i;
			var tbl=document.getElementById('remove_table');
			var tit=document.getElementById('title-'+mid).innerHTML;
			var tit='Category '+tit+' will be removed';
			if (rmids.length>1) tit+=' with all subcategories ('+(rmids.length-1)+')';
			var newtr=document.createElement("tr"); newtr.id="rem-"+mid;
			var del_btn='<A href="javascript:erase_removed('+"'"+mid+"'"+');"><img src="'+ic_del+'" align="left" border="0"></A>';
			newtd=document.createElement("td"); newtd.innerHTML=del_btn;
			newtr.appendChild(newtd);
			newtd=document.createElement("td"); newtd.innerHTML=tit;
			newtr.appendChild(newtd);
			tbl.appendChild(newtr);
			document.getElementById('removed').style.display='block';
		}
	}

	for (var i in update) {
		// that is to create submit data
		var mid=update[i].id; var pos=update[i].p; var active=update[i].a;
		var tit=update[i].t; var orig=update[i].o; var ulink=update[i].l;
		if (active) act=1; else act=0;
		update_arr[i]='update---'+mid+'---'+pos+'---'+act+'---'+tit+'---'+orig+'---'+ulink;

		if (prev==1) {
			// Just Preview
			var mid=i;
			var tbl=document.getElementById('pre_update_table');
			var tit=document.getElementById('title-'+mid).innerHTML;
			if (ulink!='0') tit+='&nbsp;&nbsp;&nbsp;[ '+ulink+' ]';

			var newtr=document.createElement("tr"); newtr.id="up-"+mid;
			
			var del_btn='<A href="javascript:erase_update('+"'"+mid+"'"+');"><img src="'+ic_del+'" align="left" border="0"></A>';
			var newtd=document.createElement("td"); newtd.innerHTML=del_btn;
			newtr.appendChild(newtd);

			newtd=document.createElement("td"); newtd.innerHTML=tit;
			newtr.appendChild(newtd);

			newtd=document.createElement("td"); newtd.innerHTML=pos; newtd.align='center';
			newtr.appendChild(newtd);

			newtd=document.createElement("td"); newtd.align='center';
			newcb=document.createElement("input");
			newcb.type='checkbox';
			newcb.checked=active; newcb.disabled=true;
			newtd.appendChild(newcb);
			newtr.appendChild(newtd);

			tbl.appendChild(newtr);
			document.getElementById('changes').style.display='block';
		}
	}
}



function edit_row (mid,itype) {
edited_rows[mid]=itype;
var icon=document.getElementById("icon-"+mid);
var sjh=document.getElementById("sw-jshref-"+mid);

var el=document.getElementById("title-ed-"+mid);
el.style.display='table';
el=document.getElementById("title-name-"+mid);
el.style.display='none';
el=document.getElementById("title-path-"+mid);
el.style.display='none';
icon.src=ic_save;
sjh.href='javascript:accept_row ('+"'"+mid+"'"+','+"'"+itype+"'"+')';
}



function accept_row (mid,itype) {
delete edited_rows[mid];
var icon=document.getElementById("icon-"+mid);
var sjh=document.getElementById("sw-jshref-"+mid);

var ted=document.getElementById("title-ed-"+mid);
var tn=document.getElementById("title-name-"+mid);
var tp=document.getElementById("title-path-"+mid);
var tne=document.getElementById("title-name-ed-"+mid);

var tno=document.getElementById("title-name-old-"+mid);

if (itype=='ucat') {
	var tl=document.getElementById("title-link-"+mid);
	var tlo=document.getElementById("title-link-old-"+mid);
	var tle=document.getElementById("title-link-ed-"+mid);
	tl.href=tle.value;
}

tn.innerHTML=tne.value;
ted.style.display='none';
tn.style.display='inline';
tp.style.display='inline';

icon.src=ic_edit;
sjh.href='javascript:edit_row ('+"'"+mid+"'"+','+"'"+itype+"'"+')';

if (tne.value!=tno.value || (itype=='ucat' && tle.value!=tlo.value)) {
tno.value=tne.value;
if (itype=='ucat') tlo.value=tle.value;
log_updates (mid);
}
}

function preview_changes () {
pre_update(1);
document.getElementById('maintable').style.display='none';
document.getElementById('previewtable').style.display='block';
reload_if_all_erased ();
}

function refuse_changes () {
document.getElementById('update').submit();
}

function submit_changes () {
var update_str='';
for (i in update_arr) {
update_str+=update_arr[i]+"\n";
}

if (check_added==1) update_str+=make_new_item_post_line();

document.getElementById('update_data').value=update_str;
document.getElementById('mode').value='update';
document.getElementById('update').submit();
//alert (update_str);
}

function fast_submit_changes () {
pre_update(0);
submit_changes();
}


function debug_changes () {
var update_str='';
for (i in update_arr) {
update_str+=update_arr[i]+"\n";
}
if (check_added==1) update_str+=make_new_item_post_line();
alert (update_str);
}

</script>
{/literal}

