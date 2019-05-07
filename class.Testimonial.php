<?php
namespace TAS;


class Testimonial extends \TAS\Entity
{

    public $id, $img, $message, $name, $des;

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
        $rs = $GLOBALS['db']->Execute("Select * from " . $GLOBALS['Tables']['testimonial'] . " where id=" . (int) $id . " limit 1");

        if (DB::Count($rs) > 0) {
            $this->LoadFromRecordSet($rs);
            $this->isLoad = true;
        } else {
            return false;
        }
    }

    public static function GetFields($id = 0)
    {
        $fields = \TAS\Entity::GetFieldsGeneric($GLOBALS['Tables']['testimonial']);

        $user = new \TAS\User();
        unset($fields['id']);

        $fields['img']['label'] = 'Img';
$fields['img']['type'] = 'text';
$fields['img']['required'] = true;

$fields['message']['label'] = 'Message Box';
$fields['message']['type'] = 'text';
$fields['message']['required'] = true;

$fields['name']['label'] = 'Name';
$fields['name']['type'] = 'text';
$fields['name']['required'] = true;

$fields['des']['label'] = 'Des';
$fields['des']['type'] = 'text';
$fields['des']['required'] = true;


        
        if (User::WebIsLoggedIn ())
        {
            $id = new \TAS\User($_SESSION['webuserid']);
        }
        else
        {
            $id = new \TAS\User($_SESSION['userid']);
        }
        $userRole = $id->UserRoleID;
        $fields['allowlogin']['type'] = 'checkbox';
        $fields['allowlogin']['label'] = 'Allow Login';
        $fields['allowlogin']['displayorder'] = '20';
        $fields['allowlogin']['shortnote'] = '(Uncheck to User to Default Login/ Temporarily Barred)';
        if($userRole=='2')
        {
            $fields['userroleid']['label'] = 'Web Role';
            $fields['userroleid']['type'] = 'select';
            $fields['userroleid']['selecttype'] = 'query';
            $fields['userroleid']['query'] = "Select userroleid, rolename from " . $GLOBALS['Tables']['userrole'] . " WHERE 1 and userroleid >1 ORDER BY rolename ASC";
            $fields['userroleid']['dbID'] = 'userroleid';
            $fields['userroleid']['dbLabelField'] = 'rolename';
            $fields['userroleid']['required'] = true;
            unset($fields['orgid']);
        }
        
        elseif($userRole > 2)
        {
            $fields['userroleid']['label'] = 'Web Role';
            $fields['userroleid']['type'] = 'select';
            $fields['userroleid']['selecttype'] = 'query';
            $fields['userroleid']['query'] = "Select userroleid, rolename from " . $GLOBALS['Tables']['userrole'] . " WHERE 1 and userroleid >2 ORDER BY rolename ASC";
            $fields['userroleid']['dbID'] = 'userroleid';
            $fields['userroleid']['dbLabelField'] = 'rolename';
            $fields['userroleid']['required'] = true;
            unset($fields['orgid']);
            unset($fields['allowlogin']);
        }
        else
        {
            $fields['userroleid']['label'] = 'Web Role';
            $fields['userroleid']['type'] = 'select';
            $fields['userroleid']['selecttype'] = 'query';
            $fields['userroleid']['query'] = "Select userroleid, rolename from " . $GLOBALS['Tables']['userrole'] . " WHERE 1 ORDER BY rolename ASC";
            $fields['userroleid']['dbID'] = 'userroleid';
            $fields['userroleid']['dbLabelField'] = 'rolename';
            $fields['userroleid']['required'] = true;
            
            $fields['orgid']['label'] = 'Organization Name';
            $fields['orgid']['type'] = 'select';
            $fields['orgid']['selecttype'] = 'query';
            $fields['orgid']['query'] = 'Select * from ' . $GLOBALS['Tables']['organization'].' where status=1';
            $fields['orgid']['dbLabelField'] = 'orgname';
            $fields['orgid']['dbID'] = 'orgid';
            $fields['orgid']['showSelect'] = true;
            
           unset($fields['gender']);
        }
        

        $fields['email']['label'] = 'Email';
        $fields['email']['type'] = 'email';
        $fields['email']['css'] = 'forminput unique-username';
        // $fields ['email'] ['displayorder'] = 1;
        $fields['phone']['label'] = 'Mobile Phone';
        $fields['phone']['type'] = 'phone';
        $fields['phone']['css'] = 'forminput unique-phone';
        $fields['phone']['required'] = true;

        

        $fields['status']['shortnote'] = '(Uncheck to User De-active)';
        $fields['status']['type'] = 'checkbox';
        $fields['status']['value'] = $user->Status == 1 ? true : false;
        $fields['status']['displayorder'] = '21';

        $fields['alertnotify']['type'] = 'checkbox';
        $fields['alertnotify']['label'] = 'Send Alert';
        $fields['alertnotify']['value'] = $user->AlertNotify == 1 ? true : false;

        unset($fields['alertnotify']);
        unset($fields['roleset']);
        if ($id > 0) {
            $user = new \TAS\User($id);
            $fields['email']['additionalattr'] = (($id > 0) ? 'data-rel="' . $user->UserID . '"' : "");
            $fields['phone']['additionalattr'] = (($id > 0) ? 'data-rel="' . $user->UserID . '"' : "");
            $a = $user->ObjectAsArray();
            foreach ($a as $i => $v) {
                if (isset($fields[strtolower($i)])) {
                    $fields[strtolower($i)]['value'] = $v;
                }
            }

           /*  $fields['adddate']['type'] = 'readonly';
            $fields['editdate']['type'] = 'readonly';
            $fields['lastlogin']['type'] = 'readonly';

            $fields['adddate']['label'] = 'Add Date';
            $fields['editdate']['label'] = 'Edit Date';
            $fields['lastlogin']['label'] = 'Last Login'; */
            
            unset($fields['password']);
        } 
        unset($fields['adddate']);
        unset($fields['editdate']);
        unset($fields['lastlogin']);
        return $fields;
    }

    
    public static function GetFieldsProfile($id = 0)
    {
        $fields = \TAS\Entity::GetFieldsGeneric($GLOBALS['Tables']['testimonial']);
        
        $user = new \TAS\User();
       
       
        
        $fields['firstname']['label'] = 'First Name';
        $fields['firstname']['required'] = true;
        
        $fields['lastname']['label'] = 'Last Name';
       
        
        
        $fields['email']['label'] = 'Email';
        $fields['email']['type'] = 'email';
        $fields['email']['css'] = 'forminput unique-username';
        $fields['email']['type'] = 'readonly';
        // $fields ['email'] ['displayorder'] = 1;
        $fields['phone']['label'] = 'Mobile Phone';
        $fields['phone']['type'] = 'phone';
        $fields['phone']['css'] = 'forminput unique-phone';
        $fields['phone']['required'] = true;
        
        
        
        
        
        unset($fields['alertnotify']);
        unset($fields['roleset']);
        if ($id > 0) {
            $user = new \TAS\User($id);
            $fields['email']['additionalattr'] = (($id > 0) ? 'data-rel="' . $user->UserID . '"' : "");
            $fields['phone']['additionalattr'] = (($id > 0) ? 'data-rel="' . $user->UserID . '"' : "");
            $a = $user->ObjectAsArray();
            foreach ($a as $i => $v) {
                if (isset($fields[strtolower($i)])) {
                    $fields[strtolower($i)]['value'] = $v;
                }
            }
        }
        unset($fields['username']);
        
        unset($fields['tag']);
        unset($fields['orgid']);
        unset($fields['allowlogin']);
        unset($fields['status']);
        unset($fields['userid']);
        unset($fields['userroleid']);
        unset($fields['password']);
        unset($fields['adddate']);
        unset($fields['editdate']);
        unset($fields['lastlogin']);
        return $fields;
    }
    
    public static function GetFieldsUser($id = 0)
    {
        $fields = \TAS\Entity::GetFieldsGeneric($GLOBALS['Tables']['testimonial']);
        
        $user = new \TAS\User();
        
        unset($fields['orgid']);
        unset($fields['username']);
        unset($fields['password']);
        unset($fields['firstname']);
        unset($fields['lastname']);
        unset($fields['email']);
        unset($fields['phone']);
        unset($fields['status']);
        unset($fields['allowlogin']);
        unset($fields['adddate']);
        unset($fields['lastlogin']);
        unset($fields['editdate']);
        unset($fields['roleset']);
        unset($fields['tag']);
        unset($fields['gender']);
        unset($fields['userroleid']);
        
        $fields['userid']['label'] = 'User Name';
        $fields['userid']['type'] = 'select';
        $fields['userid']['selecttype'] = 'query';
        $fields['userid']['query'] = "Select concat(firstname,' ',lastname) as name,userid from " . $GLOBALS['Tables']['testimonial'] . " WHERE roleset =3 ORDER BY userid ASC";
        $fields['userid']['dbID'] = 'userid';
        $fields['userid']['dbLabelField'] = 'name';
        $fields['userid']['required'] = true;
        return $fields;
    }
    
    
    
    public static function GetFieldsUserAdmin($id = 0)
    {
        $fields = \TAS\Entity::GetFieldsGeneric($GLOBALS['Tables']['testimonial']);
        
        $user = new \TAS\User();
        
        unset($fields['orgid']);
        unset($fields['username']);
        unset($fields['password']);
        unset($fields['firstname']);
        unset($fields['lastname']);
        unset($fields['email']);
        unset($fields['phone']);
        unset($fields['status']);
        unset($fields['allowlogin']);
        unset($fields['adddate']);
        unset($fields['lastlogin']);
        unset($fields['editdate']);
        unset($fields['roleset']);
        unset($fields['tag']);
        unset($fields['gender']);
        unset($fields['userroleid']);
        
        $fields['userid']['label'] = 'User Name';
        $fields['userid']['type'] = 'select';
        $fields['userid']['selecttype'] = 'query';
        $fields['userid']['query'] = "Select concat(firstname,' ',lastname) as name,userid from " . $GLOBALS['Tables']['testimonial'] . " WHERE userroleid  !=1 and roleset < 4  ORDER BY userid ASC";
        $fields['userid']['dbID'] = 'userid';
        $fields['userid']['dbLabelField'] = 'name';
        $fields['userid']['required'] = true;
        return $fields;
    }
    
    /**
     * Test is given username and email is unique in system
     *
     * @param array $d
     *            An Array with key as username and email.
     */
    public static function UniqueUser($d, $id = 0)
    {
      if ($id == 0) {
            $count = $GLOBALS['db']->ExecuteScalar("select count(*) from " . $GLOBALS['Tables']['testimonial'] . " where username='" . $d['username'] . "'");
        } else {
            $count = $GLOBALS['db']->ExecuteScalar("select count(*) from " . $GLOBALS['Tables']['testimonial'] . " where username='" . $d['username'] . "'
				and userid != '" . (int) $id . "' ");
        }

        if ($count > 0) {
            return false;
        } else {
            return true;
        }
    }
    
   
    /**
     * Check if user is referred by someone, if yes return UserID, else return bool false.
     *
     * @param array $values
     *            User ID to check if he is referred to us
     * @return number|boolean false is not, else numeric userid of who refer them.
     */
    public static function Add($values = array())
    {
       
        
        if (empty($values['username'])) {
            self::SetError("Name is required", "10");
            return false;
            
        } else if (! self::Validate($values, $GLOBALS['Tables']['testimonial'])) {
            self::SetError("Please use unique user name", "10");
            return false;
            
        } else if (! \TAS\User::UniqueUser($values)) {
            //echo "testhere" ; exit ;
            
            self::SetError("Please use unique user name", "10");
            return false;
        } else {
           
            if (isset($values['phone'])) {
                $values['phone'] = preg_replace('/[^0-9]/', '', $values['phone']);
                
                if ($GLOBALS['db']->Insert($GLOBALS['Tables']['testimonial'], $values)) {
                    $id = $GLOBALS['db']->GeneratedID();
                    return ($id);
                }
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

            $values['editdate'] = Date("Y-m-d H:i:s");
        }
        if (isset($values['phone']))
            $values['phone'] = preg_replace('/[^0-9]/', '', $values['phone']);

        unset($values['fullname']);

        if (! self::Validate($values, 'user')) {
            return false;
        }else if (! User::UniqueUser($values, $values['userid'])) {
            self::SetError("Please use unique user name", "10");
            return false;
        } else {
            if ($GLOBALS['db']->Update($GLOBALS['Tables']['testimonial'], $values, $values['userid'], 'userid')) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    
    public function UpdateUser($values = array())
    {
        
        if (is_null($values) || count($values) == 0) {
            $tv = json_decode($this->ToJson(), true);
            foreach ($tv as $k => $v) {
                $values[strtolower($k)] = $v;
            }
            
            $values['editdate'] = Date("Y-m-d H:i:s");
        }
        if (isset($values['phone']))
            $values['phone'] = preg_replace('/[^0-9]/', '', $values['phone']);
            
            unset($values['fullname']);
            
            if (! self::Validate($values, 'user')) {
                return false;
            } else {
                if ($GLOBALS['db']->Update($GLOBALS['Tables']['testimonial'], $values, $values['userid'], 'userid')) {
                    return true;
                } else {
                    return false;
                }
            }
    }

    /**
     * Delete the User
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
        $idData = new User($id);
        $orgid = $idData->OrgID;
        $roleset = $idData->RoleSet;
        if($roleset=='1')
        {
            $delete = $GLOBALS['db']->Execute("Delete from " . $GLOBALS['Tables']['testimonial'] . " where orgid=" . (int) $orgid . "");
            return true;
        }
        else
        {
            $delete = $GLOBALS['db']->Execute("Delete from " . $GLOBALS['Tables']['testimonial'] . " where userid=" . (int) $id . " limit 1");
            return true;
        }
        
        //$delete = $GLOBALS['db']->Execute("Delete from " . $GLOBALS['Tables']['userlocation'] . " where userid=" . (int) $id . " ");
        
    }

    /**
     * returns true is user is logged in.
     *
     * @return boolean
     */
    public static function IsLoggedIn()
    {
        return (isset($_SESSION['userid']) && (int) $_SESSION['userid'] > 0) ? true : false;
    }

    public static function WebIsLoggedIn()
    {
        return (isset($_SESSION['webuserid']) && (int) $_SESSION['webuserid'] > 0) ? true : false;
    }
    /**
     * Return UserId if user found with login, else return boolean false.
     *
     * @param string $username
     * @param string $password
     */
    public static function AuthenticateUser($username, $password)
    {
        if (trim($username) == "" || trim($password) == "") {
            return false;
        } else {
            $user = $GLOBALS['db']->ExecuteScalarRow("Select userid, password from " . $GLOBALS['Tables']['testimonial'] . " where username='" . $username . "'
				and status=1 and allowlogin=1 and roleset< 3 limit 1");

            if ($user) {
                $id = $user['userid'];
                $password = password_verify($password, $user['password']);
                if (is_numeric($id) && $id > 0 && is_bool($password) == true && $password === true) {
                    $GLOBALS['db']->Execute("UPDATE " . $GLOBALS['Tables']['testimonial'] . " set lastlogin=now() where userid=" . $id);
                    return $id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
    
    public static function AuthenticateUserWeb($username, $password)
    {
        if (trim($username) == "" || trim($password) == "") {
            return false;
        } else {
            $user = $GLOBALS['db']->ExecuteScalarRow("Select userid, password from " . $GLOBALS['Tables']['testimonial'] . " where username='" . $username . "'
				and status=1 and roleset > 2 and allowlogin='1'limit 1");
            
            if ($user) {
                $id = $user['userid'];
                $password = password_verify($password, $user['password']);
                if (is_numeric($id) && $id > 0 && is_bool($password) == true && $password === true) {
                    $_SESSION['webuserid'] = $id;
                    
                    
                    $GLOBALS['db']->Execute("UPDATE " . $GLOBALS['Tables']['testimonial'] . " set lastlogin=now() where userid=" . $id);
                    return $id;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Send Verification Email for User
     */
    public static function SendVerificationEmail($id)
    {
        if (is_numeric($id) && (int) $id > 0) {
            $user = new \TAS\User($id);
            if ($user->IsLoaded()) {
                $userArray = $user->EmailKeywords();
                $userArray['hash'] = md5(strtolower($user->Username) . strtolower($user->AddDate));
                $userArray['verify'] = $GLOBALS['AppConfig']['HomeURL'] . "/verifyaccount.php?hash=" . $userArray['hash'] . "&email=" . $user->Email;

                return DoEmail(2, $userArray, $user->Email, "noreply@truearrowsoftware.com");
            }
        }
        return false;
    }

    /**
     * Send welcome email Email for User
     */
    public static function SendWelcomeEmail($id)
    {
        if (is_numeric($id) && (int) $id > 0) {
            $user = new \TAS\User($id);
            if ($user->IsLoaded()) {
                $userArray = $user->EmailKeywords();
                $userArray['login'] = $GLOBALS['AppConfig']['HomeURL'] . "/login.php";
                $sender = $GLOBALS['AppConfig']['SenderEmail'];
                $attachment = $GLOBALS['AppConfig']['PhysicalPath'] . DIRECTORY_SEPARATOR . "theme" . DIRECTORY_SEPARATOR . "sbtmanual.pdf";
                return DoEmail(4, $userArray, $user->Email, $sender, $attachment);
            }
        }
        return false;
    }

    /**
     * Verify an account for email.
     *
     * @param unknown $email
     * @param unknown $hash
     * @return boolean
     */
    public static function VerifyAccount($email, $hash)
    {
        $rs = $GLOBALS['db']->Execute("Select * from " . $GLOBALS['Tables']['testimonial'] . " where email='" . $email . "' and status=0");
        if (\TAS\DB::Count($rs) > 0) {
            while ($row = $GLOBALS['db']->Fetch($rs)) {
                $newhash = md5(strtolower($row['username']) . strtolower($row['adddate']));
                // echo $newhash;
                if ($newhash == $hash) {
                    $GLOBALS['db']->Execute("update " . $GLOBALS['Tables']['testimonial'] . " set status=1,allowlogin=1 where userid=" . (int) $row['userid']);
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * Reset Password for user.
     *
     * @param unknown $email
     * @param unknown $username
     * @return boolean
     */
    public static function ResetPassword($email, $username)
    {
        $rs = $GLOBALS['db']->Execute("Select * from " . $GLOBALS['Tables']['testimonial'] . " where username='" . $username . "' and email='" . $email . "'");
        if (\TAS\DB::Count($rs) > 0) {
            while ($row = $GLOBALS['db']->Fetch($rs)) {
                $row['password'] = substr(md5(uniqid()), 0, 8);
                $GLOBALS['db']->Execute("Update " . $GLOBALS['Tables']['testimonial'] . " set password='" . $row['password'] . "' where userid=" . (int) $row['userid']);

                return DoEmail(1, $row, $email);
            }
            return false;
        } else {
            return false;
        }
    }

    public function SetPermissionForLocation($locationid)
    {
        $row = $GLOBALS['db']->ExecuteScalarRow("Select * from " . $GLOBALS['Tables']['userlocation'] . " where userid='" . (int) $GLOBALS['user']->UserID . "' and locationid='" . (int) $locationid . "' ");
        $GLOBALS['user']->UserRoleID = $row['roleid'];
    }

    /**
     * check Right Permission of Authenticate user
     */
    public static function Permission_AuthUser($username, $password, $location, $corelocation)
    {
        if (trim($username) == "" || trim($password) == "") {
            return false;
        } else {
            $id = self::AuthenticateUser($username, $password);
            if ($id) {
                    $GLOBALS['db']->Execute("UPDATE " . $GLOBALS['Tables']['testimonial'] . " set lastlogin=now() where memberid=" . $id);
                    $GLOBALS['user'] = new \TAS\User($id);
                    $row = $GLOBALS['db']->ExecuteScalarRow("Select * from " . $GLOBALS['Tables']['userlocation'] . " where userid='" . (int) $GLOBALS['user']->UserID . "' and locationid='" . (int) DoSecure($location) . "' ");
                    if ($row) {
                        $_SESSION['locationid'] = (int) DoSecure($location);
                        $GLOBALS['user']->SetPermissionForLocation($_SESSION['locationid']);
                        $permission = $GLOBALS['permission'];
                        if ($permission->CheckModulePermission($corelocation, $GLOBALS['user']->UserRoleID)) {
                            $_SESSION['userid'] = $id;
                            return true;
                        } else {
                            return false;
                        }
                    }
                
            }
        }
    }
}