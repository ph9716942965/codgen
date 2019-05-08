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
            'username' => 'dev',		//change
            'password' => 'dev',			//change
            'database' => 'test',//change
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

    public $Table;
 function __construct($table=null){
    parent::__construct();
    $this->Table=$table;
 }
    
function VariableGen(){
    $TABLE=[];
    $TABLE['Table']=($this->Table)?$this->Table:'user';
    echo "<pre>";
    $sql='SHOW full COLUMNS FROM '.$TABLE['Table'].';';
    $table=$this->get($sql);
    
    foreach($table as $tbl){
        $TABLE['Field'][] = $tbl['Field'];
        $TABLE['Key'] = ($tbl['Key']=='PRI')?$tbl['Field']: $TABLE['Key'];
    }
    $TABLE['obj']=$table;

return $TABLE;
   
}

private function Varname($TBL){
    $return='public ';
    foreach($TBL as $v){
        $return.= '$'.$v.', ';
    }
    $return= rtrim($return,', ');
    return $return.';';
}

private function GetFields($table,$forinlude=null){

    $fld='';

    if($forinlude==true){
        foreach($table as $fields){
        $label=($fields['Comment'])?ucwords($fields['Comment']):ucwords($fields['Field']);
        $type=(strpos($fields['Type'], 'char') !== false)?'string':'number';
            //if($fields['Key']!='PRI'){
        $fld.='
        \''.$fields['Field'].'\' => array(
            \'type\' => \''.$type.'\',
            \'name\' => \''.$label.'\'
        ),';
        $fld.="\n\n";    
            //}
        }
    }else {
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
    }
return $fld;
}

private function EmptyChecknGen($field){

}

private function Add($tbl){
    
    $AddValidation ='';
    foreach($tbl['obj'] as $fields){
        $AddValidation.='else ';
        if($fields['Null']=='NO' && $fields['Extra']!='auto_increment'){
            $size = substr($fields['Type'] , strpos($fields['Type'], "("), strpos($fields['Type'], ")"));
            //$size = substr($String , strpos($fields['Type'], "("), strpos($fields['Type'], ")"));
           
            $label=($fields['Comment'])?ucwords($fields['Comment']):ucwords($fields['Field']);
            
            $AddValidation.='if (empty($values[\''.$fields['Field'].'\'])) {
                self::SetError("'.$label .' is required", "'.$size[1].'");
                return false;
                
            }';

            $AddValidation.='else if (! self::Validate($values, $GLOBALS[\'Tables\'][\''.$this->Table.'\'])) {
                self::SetError("Please use unique '.$label.'", "'.$size[1].'");
                return false;
            }';

            if($fields['Key']=='UNI'){
                $AddValidation.=' else if (! \TAS\User::Unique'.ucwords($tbl['Table']).'($values)) {
                        self::SetError("Please use unique '.$label.'", "'.$size[1].'");
                        return false;
                    }';
            }
            
            
        }

    }
    return $AddValidation=ltrim($AddValidation,'else ');
}   

function ClassGen(){
   
    $Table=$this->VariableGen();
    $file=file_get_contents('classformat');
    $Gen=[
        '{~~CLASS~~}'=>ucfirst($Table['Table']),
        '{~~FIELDS~~}'=>$this->Varname($Table['Field']),
        '{~~Key~~}'=>$Table['Key'],
        '{~~Table~~}'=>$Table['Table'],
        '{~~Fields~~}'=>$this->GetFields($Table['obj']),
        '{~~AddValidation~~}'=>$this->Add($Table),
    ];

    print_r($Table);
    foreach($Gen as $search=>$replace){
        $file=str_replace($search,$replace,$file);
    }
    $fileName='class.'.ucfirst($Table['Table']).'.php';
    file_put_contents($fileName,$file);
}

private function filter($tbl,$form=false){
   // echo "191";print_r($tbl);exit;
    // $TableObj=$tbl['obj'];

    $filter='';
    foreach($tbl['obj'] as $field){
        if($field['Key']!='PRI'){
            if($form){

                $label=($field['Comment'])?ucwords($field['Comment']):ucwords($field['Field']);
            
                $filter.= '<div class="formfield">
                <label class="formlabel" for="'.$field['Field'].'">'.$label.'</label>
                <div class="forminputwrapper">
                <input type="text" name="'.$field['Field'].'" id="'.$field['Field'].'" class="forminput" value="\' . (isset($filterOptions[\''.$field['Field'].'\']) ? $filterOptions[\''.$field['Field'].'\'] : \'\') . \'" />
                </div>
                <div class="clear"></div></div>';;  
            } else {
       
            $filter.=  '$filter[] = "l.'.$field['Field'].' LIKE \'%" . DoSecure($filterOptions[\''.$field['Field'].'\']) . "%\'";';  
       
        }
        }

    }
    return $filter;
    // $tpl= 
    // '$filter[] = "l.listname LIKE \'%" . DoSecure($filterOptions[\'listname\']) . "%\'"';

    
}



public function includeGenrator()
{
    $Table=$this->VariableGen();
    $file=file_get_contents('curd/include.php');
    $fileHTML=file_get_contents('curd/index.php');
    $AddPHP=file_get_contents('curd/add.php');
    $EditPHP=file_get_contents('curd/edit.php');
    
    $Gen=[
        '{~~CLASS~~}'=>ucfirst($Table['Table']),
        '{~~FIELDS~~}'=>$this->Varname($Table['Field']),
        '{~~Key~~}'=>$Table['Key'],
        '{~~Table~~}'=>$Table['Table'],
        '{~~Fields~~}'=>$this->GetFields($Table['obj'],true),
        '{~~AddValidation~~}'=>$this->Add($Table),
        '{~~Filter~~}'=>$this->filter($Table),
        '{~~FilterHTML~~}'=>$this->filter($Table,true),
    ];
    print_r($Table);

    foreach($Gen as $search=>$replace){
        $file=str_replace($search,$replace,$file);
        $fileHTML=str_replace($search,$replace,$fileHTML);
        $AddPHP=str_replace($search,$replace,$AddPHP);
        $EditPHP=str_replace($search,$replace,$EditPHP);
    }
    mkdir($Table['Table']);
    $fileName=$Table['Table'].'/'.'include.php';

    file_put_contents($fileName,$file);
    file_put_contents($Table['Table'].'/'.'index.php',$fileHTML);
    file_put_contents($Table['Table'].'/'.'add.php',$AddPHP);
    file_put_contents($Table['Table'].'/'.'edit.php',$EditPHP);
    

}

public function CurdGenrator(){
    $temp='<div class="formfield">
    <label class="formlabel" for="name">Name</label>
    <div class="forminputwrapper">
    <input type="text" name="name" id="name" class="forminput" value="' . (isset($filterOptions['name']) ? $filterOptions['name'] : '') . '" />
    </div>
    <div class="clear"></div></div>';

    echo $temp;
}



}




$obj=new Gen("country");
//$obj->CurdGenrator(); 
$obj->ClassGen(); // Complete

$obj->includeGenrator();
