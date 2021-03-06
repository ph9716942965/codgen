<?php
require_once("./include.php");
require("../template.php");

$messages = array ();
if (! $permission->CheckOperationPermission('{~~Table~~}', 'access', $GLOBALS['user']->UserRoleID)) {
    Redirect("../index.php");
}
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['delete']) && is_numeric($_GET['delete']) && $permission->CheckOperationPermission('{~~Table~~}', 'delete', $GLOBALS['user']->UserRoleID)) {
	    if(\TAS\{~~CLASS~~}::Delete((int)$_GET['delete'])) {
			$messages[] = array("message" => _("Record delete Successfully!!!"),"level"=>10);
		} else {
			$messages[] = array("message" => _("Fail to delete this record"),"level"=>10);
		}
	}

	
	
	
	if (isset($_GET['type']) && is_numeric($_GET['id'])) {
	    if ($_GET['type'] == 'status') {
	        $u = new \TAS\{~~CLASS~~}((int) $_GET['id']);
	        if ($u->IsLoaded()) {
	            $s = (($u->Status == 1) ? 0 : 1);
	            $GLOBALS['db']->Execute("update " . $GLOBALS['Tables']['{~~Table~~}'] . " set status=" . $s . " where {~~Key~~}=" . $u->{~~Key~~});
				$_SESSION['message']='update';
                Redirect('index.php');
	        } else {
	            $messages[] = array(
	                "message" => _("No user found to update status"),
	                "level" => 10
	            );
	        }
	    }
	}
	
}

if(isset($_SESSION['message']))
{
	$messages[] = array(
		"message" => _("Status updates successfully"),
		"level" => 1
	);
	unset($_SESSION['message']);
}

if (isset($_COOKIE['{~~Table~~}_filter']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $filterOptions = json_decode($_COOKIE['{~~Table~~}_filter'], true);
} else {
    $filterOptions = $_REQUEST;
}

if(isset($_GET['mode']) && $_GET['mode'] =='clearfilter'){
    setcookie('{~~Table~~}_filter', '', (time() - 25292000 ) );
    Redirect("index.php?d=1");
}
$pageParse['Content'] .= '<h2>List Management</h2>';
$pageParse['Content'] .= \TAS\Utility::UIMessageDisplay($messages);
$pageParse['Content'] .= '<p><a href="add.php">Add New {~~CLASS~~}</a></p>';

$pageParse['Content'] .= '
  <input type="button" name="filter" id="filter" class="ui-button" value="Show Filters"/>
  <a href="index.php?mode=clearfilter"> Clear Filter</a>
  <div id="filterbox"><form method="post">
  <fieldset class="shortfields">
        
  {~~FilterHTML~~}


  <p>&nbsp;</p>
  <div class="formfield">
  <input type="submit" name="submit" id="filtersubmit" value="Submit" /> <br />
  <div class="clear"></div></div>
  </fieldset>
  </form></div><br />';

$pageParse['Content'] .= DisplayGrid();
$pageParse['FooterInclusion'] .= '<script type="text/javascript">
$(function(){
	$("#filter").click(function(){
		$("#filterbox").slideToggle().queue(function(){;
			if($("#filterbox").is(":visible")) {
				$("#filter").val("Hide Filters");
			} else {
				$("#filter").val("Show Filters");
			}
			$(this).dequeue();
		});
	});
	$("#filterbox").hide();
});
	</script>';

\TAS\TemplateHandler::TemplateChooser(5);