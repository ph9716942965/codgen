<?php
require("../template.php");
require_once("./include.php");
$messages = array();
${~~Key~~} = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (${~~Key~~} <= 0 || ! $permission->CheckOperationPermission("{~~Table~~}", "edit", $GLOBALS['user']->UserRoleID)) {
    Redirect("index.php");
}
${~~Table~~} = new \TAS\{~~CLASS~~}(${~~Key~~});

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $d = \TAS\Entity::ParsePostToArray(\TAS\{~~CLASS~~}::GetFields(${~~Key~~}));
    $isupdated = ${~~Table~~}->Update($d);
    if ($isupdated) {
        $messages[] = array(
            "message" => _("{~~CLASS~~} record updated successfully"),
            "level" => 1
        );
    } else {
        $messages[] = array(
            "message" => _("Unable to update {~~CLASS~~} record. Check data again!!!"),
            "level" => 10
        );
    }
}

$pageParse['Content'] = DisplayForm(${~~Key~~});
\TAS\TemplateHandler::TemplateChooser(5);