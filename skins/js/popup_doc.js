function cw_popup_doc(form, element_id, docs_type) {
    docs_type_value = document.getElementById(docs_type);
    return window.open ("index.php?target=popup_docs&target_form="+form+"&element_id="+element_id+"&docs_type="+docs_type_value.value, "select_doc", "width=800,height=550,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
}
