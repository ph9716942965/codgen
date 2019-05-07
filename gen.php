<?php

class Database {
    protected $db;
    protected $active_group = 'local';
    protected $link;
    function __construct(){
       // echo 'sory';exit;
        $this->db['local'] = array(
            'dsn'   => '',
            'hostname' => 'localhost',//change host name
            'username' => 'root',		//change
            'password' => '',			//change
            'database' => 'gym',//change
            'dbprefix' => '',
        );
        $this->db['serverskm'] = array(
            'dsn'   => '',
            'hostname' => '109.235.64.249',
            'username' => 'conserv_skm',
            'password' => 'P@ssw0rd',
            'database' => 'conserv_skm',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
        ); 
    
    $this->link = mysqli_connect($this->db[$this->active_group]['hostname'], $this->db[$this->active_group]['username'], $this->db[$this->active_group]['password'],$this->db[$this->active_group]['database']);
    }
    
	function query($query){
        $result=mysqli_query($this->link,$query);
        if($result){
            return $result;
        }else{
            return 0;
        }
    }
    
    function result($result){ 
        $res=array();
        while ($row = $result->fetch_assoc()) {
            $res[]=$row;
        }
        return $res;
    }
	
function con(){
        if (!$this->link) {
            die('Could not connect: ' . mysqli_error());
            }
            echo 'Connected successfully';
    }
	
	
function get($query){
    $result=$this->query($query);
    return $this->result($result);
}

    function __destruct(){
        mysqli_close($this->link);
        //echo "mysql_close";
    }
}

class Gen extends Database{
    public $UserID=5, $Username, $Password, $FullName, $FirstName, $LastName, $Email, $UserRoleID;

    public $Phone,$OrgID,$Tag,$Status, $AllowLogin, $AlertNotify, $AddDate, $EditDate, $LastLogin,$RoleSet,$Gender;
 

function VariableGen(){

    // $var=[
    //     'fields'=>['UserID','Username','Password'],
    //     'primary'=>'UserID',
    //     'table'=>'mytbl',
    // ];
    
    echo "<pre>";
    $sql='SHOW full COLUMNS FROM testimonial;';
    $table=$this->get($sql);
    $TABLE=[];
    $TABLE['Table']='testimonial';
    foreach($table as $tbl){
        $TABLE['Field'][] = $tbl['Field'];
        $TABLE['Key'] = ($tbl['Key']=='PRI')?$tbl['Field']: $TABLE['Key'];
    }
    $TABLE['obj']=$table;
/*******
 * $fields['password']['label'] = 'Password';
        $fields['password']['type'] = 'password';
        $fields['password']['required'] = true;
 */
// $fld='';

//  foreach($table as $fields){
//     if($fields['Key']!='PRI'){

//         $label=($fields['Comment'])?ucwords($fields['Comment']):ucwords($fields['Field']);
//         $type=(strpos($fields['Type'], 'char') !== false)?'text':'number';
//         $required=($fields['Null']=='NO')?true:false;
//         $fld.='$fields[\''.$fields['Field'].'\'][\'label\'] = \''.$label.'\';'."\n";
//         $fld.='$fields[\''.$fields['Field'].'\'][\'type\'] = \''.$type.'\';'."\n"; //Password|cb|readonly|checkbox
//         $fld.=($fields['Null']=='NO')
//                 ?'$fields[\''.$fields['Field'].'\'][\'required\'] = \'true\';':'';
//         $fld.="\n\n";
//     }
   
//     echo $fld;
    // $fields['password']['type'] = 'password';
    // $fields['password']['required'] = true;
 
   // print_r(($table));exit;
return $TABLE;
//print_r(($TABLE));
   
}

private function Varname($TBL){
    $return='public ';
    //echo "hii";print_r($TBL);exit;
    foreach($TBL as $v){
        $return.= '$'.$v.', ';
    }
    $return= rtrim($return,', ');
    return $return.';';
}

private function GetFields($table){
    // $sql='SHOW full COLUMNS FROM testimonial;';
    // $table=$this->get($sql);
    $fld='';

 foreach($table as $fields){
    if($fields['Key']!='PRI'){
        $label=($fields['Comment'])?ucwords($fields['Comment']):ucwords($fields['Field']);
        $type=(strpos($fields['Type'], 'char') !== false)?'text':'number';
        $required=($fields['Null']=='NO')?true:false;
        $fld.='$fields[\''.$fields['Field'].'\'][\'label\'] = \''.$label.'\';'."\n";
        $fld.='$fields[\''.$fields['Field'].'\'][\'type\'] = \''.$type.'\';'."\n"; //Password|cb|readonly|checkbox
        $fld.=($fields['Null']=='NO')
                ?'$fields[\''.$fields['Field'].'\'][\'required\'] = true;':'';
        $fld.="\n\n";
    }
}
return $fld;
}
function ClassGen(){
   
    $Table=$this->VariableGen();
    $file=file_get_contents('plainclass');
    $Gen=[
        '{~~CLASS~~}'=>ucfirst($Table['Table']),
        '{~~FIELDS~~}'=>$this->Varname($Table['Field']),
        '{~~Key~~}'=>$Table['Key'],
        '{~~Table~~}'=>$Table['Table'],
        '{~~Fields~~}'=>$this->GetFields($Table['obj']),
    ];

    foreach($Gen as $search=>$replace){
        $file=str_replace($search,$replace,$file);
    }

    $fileName='class.'.ucfirst($Table['Table']).'.php';
    file_put_contents($fileName,$file);
}

}



$obj=new Gen;
$obj->ClassGen();
 
 