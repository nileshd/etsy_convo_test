<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

include_once 'application/models/container/user_info.php';
require_once('application/libraries/exceptions/EtsyException.php');

/**
 * User Model helps to perform all the database related functionalities for the user
 *
 * @category User
 * @author Nilesh Dosooye
 * @version 1.0
 * @name User_Model
 * @access public
 */
class Users_Model extends Base_Model
{


    public function __construct()
    {
        parent::__construct();
        $this->_table = "users";
    }


    public function getById($user_d)
    {

    	$user_obj = parent::getById($user_d);
    	return $user_obj;
    }


}