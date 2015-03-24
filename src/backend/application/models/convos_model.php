<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

require_once('application/libraries/exceptions/EtsyException.php');

/**
 * Convos Model helps to perform all the database related functionalities for the convos
 *
 * @category Convos
 * @author Nilesh Dosooye
 * @version 1.0
 * @name Convos_Model
 * @access public
 */
class Convos_Model extends Base_Model
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

    public function getByRecipientId($recipient_id)
    {

    	$sql =<<<EOT

    	SELECT  c.id,c.subject,
        (SELECT count(*) FROM convos WHERE root_parent_id = c.id ) as thread_count
        FROM convos c
        WHERE c.parent_id = 0
        GROUP BY c.id

EOT;




    	$user_obj = parent::getById($user_d);
    	return $user_obj;
    }


}