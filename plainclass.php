<?php
namespace TAS;


class {~~CLASS~~} extends \TAS\Entity
{

    {~~FIELDS~~}

    public function __construct($id = 0)
    {
        parent::__construct();
        if (is_numeric($id) && $id > 0) {
            $this->Load($id);
        }
    }

    public function Load($id = 0)
    {
        if (! is_numeric($id) || (int) $id <= 0) {
            if ($this->{~~Key~~} > 0) {
                $id = $this->{~~Key~~};
            } else {
                return false;
            }
        }
        $rs = $GLOBALS['db']->Execute("Select * from " . $GLOBALS['Tables']['{~~Table~~}'] . " where {~~Key~~}=" . (int) $id . " limit 1");

        if (DB::Count($rs) > 0) {
            $this->LoadFromRecordSet($rs);
            $this->isLoad = true;
        } else {
            return false;
        }
    }

    public static function GetFields($id = 0)
    {
        $fields = \TAS\Entity::GetFieldsGeneric($GLOBALS['Tables']['{~~Table~~}']);
        ${~~Table~~} = new \TAS\{~~CLASS~~}();
        unset($fields['{~~Key~~}']);
        {~~Fields~~}


        if ($id > 0) {
            ${~~Table~~} = new \TAS\{~~CLASS~~}($id);
            
            $a = ${~~Table~~}->ObjectAsArray();
            foreach ($a as $i => $v) {
                if (isset($fields[strtolower($i)])) {
                    $fields[strtolower($i)]['value'] = $v;
                }
            }

            $fields['adddate']['type'] = 'readonly';
            $fields['editdate']['type'] = 'readonly';

            $fields['adddate']['label'] = 'Add Date';
            $fields['editdate']['label'] = 'Edit Date';

            unset($fields['password']);
        } else {
            unset($fields['adddate']);
            unset($fields['editdate']);
           
        }
        
        return $fields;
    }
    
    public static function Add($values = array())
    {
        {~~AddValidation~~}
        else if (! self::Validate($values, $GLOBALS['Tables']['user'])) {
            self::SetError("Please use unique user name", "10");
            return false;
        } else if (! \TAS\User::UniqueUser($values)) {
            // echo "testhere" ; exit ;
            self::SetError("Please use unique user name", "10");
            return false;
        } else {
               
                if ($GLOBALS['db']->Insert($GLOBALS['Tables']['user'], $values)) {
                    $id = $GLOBALS['db']->GeneratedID();
                    return ($id);
                } else {
                return false;
            }
        }
        
    }

    



   
    public function Update($values = array())
    {
         
        if (is_null($values) || count($values) == 0) {
            $tv = json_decode($this->ToJson(), true);
            foreach ($tv as $k => $v) {
                $values[strtolower($k)] = $v;
            }

            //$values['editdate'] = Date("Y-m-d H:i:s");  TIMESTAMP
        }
        
        

        if (! self::Validate($values, '{~~Table~~}')) {
            return false;
        }
        // else if (! {~~CLASS~~}::Unique{~~CLASS~~}Name($values, $values['{~~Key~~}'])) {
        //     self::SetError("Please use unique {~~CLASS~~} name", "10");
        //     return false;
        // }
         else {
            if ($GLOBALS['db']->Update($GLOBALS['Tables']['{~~Table~~}'], $values, $values['{~~Key~~}'], '{~~Key~~}')) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    
  

    /**
     * Delete the {~~Table~~}
     *
     * @param integer $id
     * @return boolean
     */

    public static function Delete($id)
    {
        if (! is_numeric($id) || (int) $id <= 0) {
            return false;
        }
        $id = floor((int) $id);
        
        $delete = $GLOBALS['db']->Execute("Delete from " . $GLOBALS['Tables']['{~~Table~~}'] . " where {~~Key~~}=" . (int) $id . " limit 1");
        return true;
    }
    

    /**
     * Check Unique Validation
     *
     * @param integer $id
     * @return boolean
     */


    public static function Unique{~~CLASS~~}Name($d, ${~~Key~~} = 0)
    {
        if (${~~Key~~} == 0) {
            $count = $GLOBALS['db']->ExecuteScalar("select count(*) from " . $GLOBALS['Tables']['{~~Table~~}'] . " where {~~Table~~}name='" . $d['{~~Table~~}name'] . "'");
        } else {
            $count = $GLOBALS['db']->ExecuteScalar("select count(*) from " . $GLOBALS['Tables']['{~~Table~~}'] . " where {~~Table~~}name='" . $d['{~~Table~~}name'] . "'
				and {~~Key~~} != '" . (int) ${~~Key~~} . "' ");
        }

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }


  
   


}