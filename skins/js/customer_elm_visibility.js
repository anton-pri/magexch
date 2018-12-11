function switch_elm_visibility(checkbox, elmName, func) {
    elm = document.getElementById(elmName);
    checkbox_elm = document.getElementById(checkbox);
    if (elm) {
        if (elm.style.display == "none") {
            elm.style.display = "";
            if (checkbox_elm)
                document.getElementById(checkbox).className = 'adv_minus'
        }
        else {
            elm.style.display = "none";
            if (checkbox_elm)
                document.getElementById(checkbox).className = 'adv_plus'
        }
    }
    if (isset(func))
        eval(func);
}
