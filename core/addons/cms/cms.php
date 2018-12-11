<?php
if ($mode == 'add' || !empty($contentsection_id)) {
	cw_include('addons/cms/cs_banner.php');
} else {
	cw_include('addons/cms/cs_banners.php');
}
