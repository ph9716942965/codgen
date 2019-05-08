<?php
require ("../template.php");
require_once ("./include.php");
if (! $permission->CheckOperationPermission('{~~Table~~}', 'add', $GLOBALS['user']->UserRoleID)) {
    Redirect("index.php");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $d = \TAS\Entity::ParsePostToArray(\TAS\{~~CLASS~~}::GetFields());
    // $d['adddate'] = date("Y-m-d h:i:s"); TIMESTAMP
    
    ${~~Key~~} = \TAS\{~~CLASS~~}::Add($d);
    
    if (${~~Key~~} > 0) {
        $messages[] = array(
            "message" => _("{~~CLASS~~} added successfully!!!"),
            "level" => 1
        );
    } else {
        $messages[] = array(
            "message" => _("Unable to create {~~CLASS~~} at this moment. Please try again later!!!"),
            "level" => 10
        );
    }
}
$pageParse['Content'] .= DisplayForm();
\TAS\TemplateHandler::TemplateChooser(5);