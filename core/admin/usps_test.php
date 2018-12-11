<?php
# kornev, TOFIX
require $app_main_dir."/addons/shipping_label_generator/usps_test.php";
cw_header_location("index.php?target=popup_info&action=TSTLBL");
