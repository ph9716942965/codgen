<?php
require ("../template.php");
require_once ("./include.php");
if (! $permission->CheckOperationPermission('country', 'add', $GLOBALS['user']->UserRoleID)) {
    Redirect("index.php");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $d = \TAS\Entity::ParsePostToArray(\TAS\Country::GetFields());
    // $d['adddate'] = date("Y-m-d h:i:s"); TIMESTAMP
    
    $id = \TAS\Country::Add($d);
    
    if ($id > 0) {
        $messages[] = array(
            "message" => _("Country added successfully!!!"),
            "level" => 1
        );
    } else {
        $messages[] = array(
            "message" => _("Unable to create Country at this moment. Please try again later!!!"),
            "level" => 10
        );
    }
}
$pageParse['Content'] .= DisplayForm();
\TAS\TemplateHandler::TemplateChooser(5);