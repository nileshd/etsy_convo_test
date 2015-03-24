<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}


/**
 *
 * Controller which handle Etsy Users
 *
 * @author Nilesh Dosooye
 * @version 1.0
 * @name Etsy_user
 * @access public
 *
 */
class Etsy_user extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->model('users_Model');
        $this->load->model('convos_Model');
    }




    /**
     * @param array $req_params - mandatory indexes to method - id
     *
     *   */
    public function id_GET($req_params)
    {
        $get_params = $this->input->get();
        // check all mandatory params
        $mandatory_params = array('id');
        $this->check_mandatory($get_params, $mandatory_params);
        $id = $this->getInputData("id", $get_params);

        $user_result = $this->users_Model->getById($id);

        if (! $user_result)
        {
            throw new EtsyException("User Not Found");
        }
        else
        {
            $response['success'] = 1;
            $response['data'] = array("user"=>$user_result);
            $this->print_json($response);
        }
    }


}
