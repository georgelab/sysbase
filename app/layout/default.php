<?php
/*
* Default layout loader
*/

$appSetup = json_decode(CoreSetup);
$view = $appSetup->paths->root . $appSetup->paths->view;
$header = $view . '/' . $appSetup->core_views->header;
$footer = $view . '/' . $appSetup->core_views->footer;

include($header);
include_once($content);
include($footer);