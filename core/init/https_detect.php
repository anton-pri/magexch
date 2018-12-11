<?php
$HTTPS_RELAY = false;
$HTTPS = (stristr($_SERVER['HTTPS'], "on") || ($_SERVER['HTTPS'] == 1) || ($_SERVER['SERVER_PORT'] == 443));
