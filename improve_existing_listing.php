<?php

require "./auth.php";

$smarty->assign("main", "improve_existing_listing");

# Assign the current location line
$smarty->assign("location", $location);

@include $xcart_dir."/modules/gold_display.php";
func_display("admin/home.tpl",$smarty);



?>