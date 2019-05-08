<?php
require("../template.php");
require_once("./include.php");
$messages = array();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0 || ! $permission->CheckOperationPermission("country", "edit", $GLOBALS['user']->UserRoleID)) {
    Redirect("index.php");
}
$country = new \TAS\Country($id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $d = \TAS\Entity::ParsePostToArray(\TAS\Country::GetFields($id));
    $isupdated = $country->Update($d);
    if ($isupdated) {
        $messages[] = array(
            "message" => _("Country record updated successfully"),
            "level" => 1
        );
    } else {
        $messages[] = array(
            "message" => _("Unable to update Country record. Check data again!!!"),
            "level" => 10
        );
    }
}

$pageParse['Content'] = DisplayForm($id);
\TAS\TemplateHandler::TemplateChooser(5);