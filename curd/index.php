<?php
require_once("./include.php");
require("../template.php");
$check = 0;
$msg = array ();
if (! $permission->CheckOperationPermission('{~~Table~~}', 'access', $GLOBALS['user']->UserRoleID)) {
    Redirect("../index.php");
}
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['delete']) && is_numeric($_GET['delete']) && $permission->CheckOperationPermission('{~~Table~~}', 'delete', $GLOBALS['user']->UserRoleID)) {
	    if(\TAS\{~~CLASS~~}::Delete((int)$_GET['delete'])) {
			$check = 1;
		} else {
			$check = 2;
		}
	}
	if(isset($check)) {
	    switch($check) {
	        case 1:
	            $messages[] = array("message" => _("Record delete Successfully!!!"),"level"=>10);
	            break;
	        case 2:
	            $messages[] = array("message" => _("Fail to delete this record"),"level"=>10);
	            break;
	    }
	}
	
	
	
	if (isset($_GET['type']) && is_numeric($_GET['id'])) {
	    if ($_GET['type'] == 'status') {
	        $u = new \TAS\{~~CLASS~~}((int) $_GET['id']);
	        if ($u->IsLoaded()) {
	            $s = (($u->Status == 1) ? 0 : 1);
	            $GLOBALS['db']->Execute("update " . $GLOBALS['Tables']['{~~Table~~}'] . " set status=" . $s . " where {~~Key~~}=" . $u->{~~Key~~});
	            $messages[] = array(
	                "message" => _("Status updates successfully"),
	                "level" => 1
	            );
	        } else {
	            $messages[] = array(
	                "message" => _("No user found to update status"),
	                "level" => 10
	            );
	        }
	    }
	}
	
}

$pageParse['Content'] .='<h2>{~~CLASS~~} Management</h2>';
$pageParse['Content'] .= \TAS\Utility::UIMessageDisplay($messages);
//$pageParse['Content'] .= '<p><a href="add.php">Add New Brand</a></p>'.DisplayGrid();


if (isset($_COOKIE['admin_location_filter']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $filterOptions = json_decode($_COOKIE['admin_location_filter'], true);
} else {
    $filterOptions = $_REQUEST;
}

if(isset($_GET['mode']) && $_GET['mode'] =='clearfilter'){
    setcookie('admin_location_filter', '', (time() - 25292000 ) );
    Redirect("index.php?d=1");
}

$pageParse['Content'] .= '
  <input type="button" name="filter" id="filter" class="ui-button" value="Show Filters"/>
  <a href="index.php?mode=clearfilter"> Clear Filter</a>
  <div id="filterbox"><form method="post">
  <fieldset class="shortfields">
        
  <div class="formfield">
  <label class="formlabel" for="name">Name</label>
  <div class="forminputwrapper">
  <input type="text" name="name" id="name" class="forminput" value="' . (isset($filterOptions['name']) ? $filterOptions['name'] : '') . '" />
  </div>
  <div class="clear"></div></div>

  <div class="formfield">
  <label class="formlabel" for="phone">Phone</label>
  <div class="forminputwrapper">
  <input type="text" name="phone" id="phone" class="forminput" value="' . (isset($filterOptions['phone']) ? $filterOptions['phone'] : '') . '" />
  </div>
  <div class="clear"></div></div>
  
  <div class="formfield">
  <label class="formlabel" for="email">Email</label>
  <div class="forminputwrapper">
  <input type="text" name="email" id="email" class="forminput" value="' . (isset($filterOptions['email']) ? $filterOptions['email'] : '') . '" />
  </div>
  <div class="clear"></div></div>

  <p>&nbsp;</p>
  <div class="formfield">
  <input type="submit" name="submit" id="filtersubmit" value="Submit" /> <br />
  <div class="clear"></div></div>
  </fieldset>
  </form></div><br />';

$pageParse['Content'] .= '<p><a href="add.php">Add New Location</a></p>'.DisplayGrid();
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
    
	$(".orderprocess").click(function(){
		return confirm("Are you sure to re-process this order with vendors?");
	});
});
	</script>';

\TAS\TemplateHandler::TemplateChooser(5);