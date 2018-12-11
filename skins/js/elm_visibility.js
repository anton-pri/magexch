function switch_elm_visibility(checkbox, elmName, func) {
    elm = document.getElementById(elmName);
    checkbox_elm = document.getElementById(checkbox);
    if (elm) {
        if (elm.style.display == "none") {
            elm.style.display = "";
            if (checkbox_elm)
                document.getElementById(checkbox).src = images_dir+'/admin/minus.png'
        }
        else {
            elm.style.display = "none";
            if (checkbox_elm)
                document.getElementById(checkbox).src = images_dir+'/admin/plus.png'
        }
    }
    if (isset(func))
        eval(func);
}
