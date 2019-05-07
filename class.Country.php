<?php
namespace TAS;


class Country extends \TAS\Entity
{

    public $id, $name;

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
            if ($this->id > 0) {
                $id = $this->id;
            } else {
                return false;
            }
        }
        $rs = $GLOBALS['db']->Execute("Select * from " . $GLOBALS['Tables']['country'] . " where id=" . (int) $id . " limit 1");

        if (DB::Count($rs) > 0) {
            $this->LoadFromRecordSet($rs);
            $this->isLoad = true;
        } else {
            return false;
        }
    }

    public static function GetFields($id = 0)
    {
        $fields = \TAS\Entity::GetFieldsGeneric($GLOBALS['Tables']['country']);
        $country = new \TAS\Country();
        unset($fields['id']);
        $fields['name']['label'] = 'Name';
$fields['name']['type'] = 'text';
$fields['name']['required'] = true;




        if ($id > 0) {
            $country = new \TAS\Country($id);
            
            $a = $country->ObjectAsArray();
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


        if (empty($values['name'])) {
                self::SetError("Name is required", "3");
                return false;
                
            }
        if (! self::Validate($values, $GLOBALS['Tables']['user'])) {
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
                }
            } else {
                return false;
            }
            
        // else if (! self::Validate($values, $GLOBALS['Tables']['country'])) {
        //     self::SetError("Please use unique user name", "10");
        //     return false;
            
        // } else if (! \TAS\User::UniqueUser($values)) {
        //     //echo "testhere" ; exit ;
            
        //     self::SetError("Please use unique user name", "10");
        //     return false;
        // } else {
           
        //     if (isset($values['phone'])) {
        //         $values['phone'] = preg_replace('/[^0-9]/', '', $values['phone']);
                
        //         if ($GLOBALS['db']->Insert($GLOBALS['Tables']['country'], $values)) {
        //             $id = $GLOBALS['db']->GeneratedID();
        //             return ($id);
        //         }
        //     } else {
        //         return false;
        //     }
        // }
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
        
        

        if (! self::Validate($values, 'country')) {
            return false;
        }
        // else if (! Country::UniqueCountryName($values, $values['id'])) {
        //     self::SetError("Please use unique Country name", "10");
        //     return false;
        // }
         else {
            if ($GLOBALS['db']->Update($GLOBALS['Tables']['country'], $values, $values['id'], 'id')) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    
  

    /**
     * Delete the country
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
        
        $delete = $GLOBALS['db']->Execute("Delete from " . $GLOBALS['Tables']['country'] . " where id=" . (int) $id . " limit 1");
        return true;
    }
    

    /**
     * Check Unique Validation
     *
     * @param integer $id
     * @return boolean
     */


    public static function UniqueCountryName($d, $id = 0)
    {
        if ($id == 0) {
            $count = $GLOBALS['db']->ExecuteScalar("select count(*) from " . $GLOBALS['Tables']['country'] . " where countryname='" . $d['countryname'] . "'");
        } else {
            $count = $GLOBALS['db']->ExecuteScalar("select count(*) from " . $GLOBALS['Tables']['country'] . " where countryname='" . $d['countryname'] . "'
				and id != '" . (int) $id . "' ");
        }

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }


  
   


}