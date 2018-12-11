<?php
// admin specific requests

if ($mode == 'memberships')
    cw_include('include/ajax/memberships.php');
if ($mode  == 'product_by_ean')
    cw_include('include/ajax/product_by_ean.php');
if ($mode == 'layout')
    cw_include('include/ajax/layout.php');
// < Old style AJAX JSON requests

cw_include('include/ajax.php');

// END
// script does not return back to this file
