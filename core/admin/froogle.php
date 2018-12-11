<?php
# kornev, TOFIX
if (empty($addons['froogle']))
	cw_header_location("index.php?target=error_message&error=access_denied&id=65");

include $app_main_dir."/addons/froogle/froogle.php";
