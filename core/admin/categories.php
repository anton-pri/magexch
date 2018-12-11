<?php
cw_load('category', 'product', 'image');

if ($mode == 'products')
    cw_include('include/categories/products.php');
elseif($mode == 'edit' || $mode == 'add')
    cw_include('include/categories/modify.php');
else 
    cw_include('include/categories/list.php');

