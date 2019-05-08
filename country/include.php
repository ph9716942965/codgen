<?php
use \TAS\DB;
function DisplayForm($edit = 0)
{
    $actionurl = ($edit > 0) ? "{AdminURL}/country/edit.php?id=" . $edit : "{AdminURL}/country/add.php";
    $formtitle = ($edit > 0) ? "Edit Country" : "Add Country";


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
  

    $SQLQuery['basicquery'] = "select * from " . $GLOBALS['Tables']['country'];
    $filterOptions = array();
    $filterOptions=(isset($_COOKIE['country_filter']) && $_SERVER['REQUEST_METHOD'] == 'GET')
            ? json_decode(stripslashes($_COOKIE['country_filter']), true)
            : $_POST;



    $filter = [];

    $filter[] = "l.name LIKE '%" . DoSecure($filterOptions['name']) . "%'";
    
   
    if (count($filter) > 0) {
        $SQLQuery['where'] = ' where ' . implode(' or ', $filter) . ' ';
    } 
    else
     {
         $SQLQuery['where'] = '';
     }
   

    $SQLQuery['pagingQuery'] = "select count(*) from " . $GLOBALS['Tables']['country'];
    
    $_COOKIE['country_filter'] = json_encode($filterOptions);
    setcookie('country_filter', json_encode($filterOptions), (time() + 25292000));

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
    

     $param['extraicons'] = $extraIcons;
    $Country = \TAS\Utility::HTMLGridFromRecordSet($SQLQuery, $pages, 'country', $param);
    return $Country;
}

