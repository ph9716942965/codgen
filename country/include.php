<?php
use \TAS\DB;
function DisplayForm($edit = 0)
{
    $actionurl = ($edit > 0) ? "{AdminURL}/country/edit.php?id=" . $edit : "{AdminURL}/country/add.php";
    $formtitle = ($edit > 0) ? "Edit Country" : "Add Country";

    // if ($edit > 0) {
	// 	$attribute = $GLOBALS['db']->ExecuteScalarRow("select * from ". $GLOBALS['Tables']['enumeration']. " where enumid=". (int)$edit);
	// } else {
	// 	$attribute = array('ekey'=>'', 'value'=>'', 'enumid'=>0);
    // }
    

    $fields = array();

     // EXTRA FIELDS
	// $fields['value'] = array (
    //         'field' =>  'value',
    //         'id' => 'value',
    //         'type' => 'varchar',
    //         'displayorder' =>  1,
    //         'value' =>$attribute['value'],  
    //         'size' =>  30,
    //         'group' =>  'basic',
    //         'label' => 'Attribute Name',
    //         'maxlength' =>  100,
    //         'required' =>  1
    //     );


    $fields = \TAS\Country::GetFields($edit);

    $param['Fields'] = $fields;
    $param['Group'] = array(
        'basic' => array(
            'legend' => ''
        )
    );
    $form = '<h2>' . _($formtitle) . '</h2>' . \TAS\Utility::UIMessageDisplay($GLOBALS['messages']) . '
<form action="" method="post" class="validate" enctype="multipart/form-data">
<fieldset class="generalform">
	<legend></legend>
    ' . \TAS\Utility::GetFormHTML($param) . '
	<div class="formbutton">
		<input type="submit" name="btnsubmit" id="btnsubmit" class="ui-button ui-state-default ui-corner-all" value="' . _("Submit") . '" />			
	</div>
</fieldset>
</form>';
    return $form;
}

function DisplayGrid()
{
   // $userid = new \TAS\User($_SESSION['userid']);
    //$userRole = $userid->UserRoleID;
   // $orgid = $userid->OrgID;

    $SQLQuery['basicquery'] = "select * from " . $GLOBALS['Tables']['country'];
    $filterOptions = array();
    $filterOptions=(isset($_COOKIE['admin_list_filter']) && $_SERVER['REQUEST_METHOD'] == 'GET')
            ? json_decode(stripslashes($_COOKIE['admin_list_filter']), true)
            : $_POST;

    // if (isset($_COOKIE['admin_list_filter']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    //     $filterOptions = json_decode(stripslashes($_COOKIE['admin_list_filter']), true);
    // } else {
    //     $filterOptions = $_POST;
    // }

    $filter = [];
    
    if (isset($filterOptions['listname']) && $filterOptions['listname'] != '') {
        $filter[] = "l.listname LIKE '%" . DoSecure($filterOptions['listname']) . "%'";
    }

    if(isset($filterOptions['orgid']))
    {
        foreach($filterOptions['orgid'] as $orgid)
        {
            $filter[] = " l.orgid like '%" . $GLOBALS['db']->Escape($orgid) . "%'";
        }
    }
    
    $orgidset = array();
    $orgidset[] = "l.orgid =" . DoSecure($orgid) . "";
    
    if (count($filter) > 0) {
        $SQLQuery['where'] = ' where ' . implode(' or ', $filter) . ' ';
    } else {
        if ($userRole != '1') {
            $SQLQuery['where'] = ' where ' . implode(' and ', $orgidset) . ' ';
        } else {
            $SQLQuery['where'] = '';
        }
    }

    $SQLQuery['pagingQuery'] = "select count(*) from " . $GLOBALS['Tables']['country'];
    
    $_COOKIE['admin_list_filter'] = json_encode($filterOptions);
    setcookie('admin_list_filter', json_encode($filterOptions), (time() + 25292000));

    $pages['gridpage'] = $GLOBALS['AppConfig']['AdminURL'] . 'country/index.php';
    $pages['edit'] = $GLOBALS['AppConfig']['AdminURL'] . 'country/edit.php';
    $pages['delete'] = $GLOBALS['AppConfig']['AdminURL'] . 'country/index.php';
    
    $param['defaultorder'] = 'id';
    $param['defaultsort'] = 'desc';
    $param['indexfield'] = 'id';
    $param['tablename'] = $GLOBALS['Tables']['country'];
    $param['fields'] = array(

        
        'id' => array(
            'type' => 'number',
            'name' => 'Id'
        ),


        'name' => array(
            'type' => 'string',
            'name' => 'Name'
        ),



        // 'listid' => array(
        //     'type' => 'string',
        //     'name' => '#'
        // ),
        // 'listname' => array(
        //     'type' => 'string',
        //     'name' => 'List Name'
        // ),
        // 'orgname' => array(
        //     'type' => 'string',
        //     'name' => 'Organisation Name'
        // ),
        // 'status' => array(
        //     'type' => 'onoff',
        //     'name' => 'Status',
        //     'mode' => 'fa'
        // )
    );
    $param['allowselection'] = false;
    $param['LinkFirstColumn'] = true;
    $param['MultiTableSearch'] = true;
    $extraIcons = array();
    
    // $extraIcons[0]['link'] = $GLOBALS['AppConfig']['AdminURL'] . 'list/productlist.php';
    // $extraIcons[0]['iconclass'] = 'fa-list';
    // $extraIcons[0]['tooltip'] = 'Add Product List';
    // $extraIcons[0]['tagname'] = 'addproductlist';
    // $extraIcons[0]['paramname'] = 'listid';
    // $extraIcons[0]['iconparent'] = 'fa';
    // $extraIcons[1]['link'] = $GLOBALS['AppConfig']['AdminURL'] . 'list/userlist.php';
    // $extraIcons[1]['iconclass'] = 'fa-user';
    // $extraIcons[1]['tooltip'] = 'User List';
    // $extraIcons[1]['tagname'] = 'adduserlist';
    // $extraIcons[1]['paramname'] = 'userid';
    // $extraIcons[1]['iconparent'] = 'fa';

     $param['extraicons'] = $extraIcons;
    //print_R($SQLQuery);
    $listing = \TAS\Utility::HTMLGridFromRecordSet($SQLQuery, $pages, 'list', $param);
    return $listing;
}

