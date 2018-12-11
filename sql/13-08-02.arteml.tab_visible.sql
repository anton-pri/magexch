-- GET[target] does not exists after init/prepare.php
UPDATE `cw_navigation_targets` SET visible=replace(visible,"_GET['target","_REQUEST['target");
