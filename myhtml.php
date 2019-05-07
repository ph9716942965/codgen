<?php
require_once('C:\xampp\htdocs\ecomm.local\cg\Form.php');
require_once('C:\xampp\htdocs\ecomm.local\cg\Formvalidation.php');
echo "<pre>";

$obj = new \HTML\Form();
$validation=new \HTML\Formvalidation();


$validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[12]');
$validation->set_rules('password', 'Password', 'required');
$validation->set_rules('passconf', 'Password Confirmation', 'required');
$validation->set_rules('email', 'Email', 'required');

echo $obj->form_fieldset ( 'Validation Form' , ['id'=> 'address_info' , 'class' => 'address_info']);
echo $validation->form_open_multipart('hiii');

echo $validation->form_input('username', '');
echo $validation->form_input('password', '');
echo $validation->form_submit('mysubmit', 'Submit Post!');

echo $validation->form_close();
echo $obj->form_fieldset_close();



if($_POST['username']){
    if ($validation->run() == FALSE)
    {
       // print_r($_SESSION);
        print_r($_REQUEST);
       // print_r($_COOKIE);
        echo "Hiii fail";//exit;
            //$this->load->view('myform');
    }
    else
    {
        echo "Pass";exit;
            //$this->load->view('formsuccess');
    }
}


$data = array(
    'name'  => 'Nasir Uddin',
    'email' => 'nasir@programmer.net',
    'url'   => 'http://facebook.com'
);
echo $obj->form_hidden($data);
//echo \HTML\Form::form_open('email/send');

$data = array(
    'name'          => 'username',
    'id'            => 'username',
    'value'         => $obj->set_value('username'),
    'maxlength'     => '100',
    'size'          => '50',
    'style'         => 'width:50%'
);
$js = 'onClick="some_function()"';
echo $obj->form_input('username', 'johndoe', $js);
echo $obj->form_password('username', 'johndoe', $js);

echo $obj->form_fieldset ( 'Login Form' , ['id'=> 'address_info' , 'class' => 'address_info']);

echo $obj->form_upload('username', 'johndoe', $js);
echo $obj->form_textarea('username', 'johndoe', $js);
echo $obj->form_multiselect('username', 'johndoe', $js);

echo $obj->form_fieldset_close();

$options = array(
    'small'         => 'Small Shirt',
    'med'           => 'Medium Shirt',
    'large'         => 'Large Shirt',
    'xlarge'        => 'Extra Large Shirt',
);

$shirts_on_sale = array('small', 'large');
echo $obj->form_dropdown('shirts', $options, 'large');

//echo $obj->form_fieldset('shirts');
echo $obj->form_checkbox('newsletter', 'accept', TRUE);
echo $obj->form_radio('newsletter', 'accept', TRUE);

echo  $obj->form_label('What is your Name', 'username',['class' => 'mycustomclass','style' => 'color: #000;']);
echo $obj->form_radio('username', 'accept', TRUE);
//echo form_submit('mysubmit', 'Submit Post!');
//form_reset
//form_button

//set_value
echo $obj->form_close();

