<?php

if ($request_prepared['cat'] != 'detailed_product_images') return;

	require_once $app_main_dir . "/addons/detailed_product_images/func.php";
	if ($REQUEST_METHOD=='GET') cw_dpi_check_viewers($_addon);
