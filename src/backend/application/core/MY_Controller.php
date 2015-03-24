<?php


if (! defined('BASEPATH'))
    exit('No direct script access allowed');

require_once('application/libraries/exceptions/EtsyException.php');

class MY_Controller extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('utils','timer'));
        $this->load->model('Base_Model');
        $this->load->helper('url');
        $this->load->library('validation');
    }


    public function _output_error($code, $message)
    {
        $this->_add_no_cache_headers();

        $response = new stdClass();
        $response->success = 0;
        $response->data->error = array("code" => $code,"message" => $message);

        $this->print_json($response);
    }


    public function _output_exception(Exception $exception)
    {

        $this->_add_no_cache_headers();

        $this->output->set_header("Content-Type: application/json; charset=utf-8");
        $response = array();
        $response['success'] = 0;
        $response['data']['error'] = array('message' => $exception->getMessage());
        $this->print_json($response);
    }


    public function front_controller()
    {

        try
        {
            $this->timer->start();
            $headers = $this->input->request_headers();

            // grabbing all url friendly params ie: /id/12/name/john
            $url_params = $this->uri->segment_array();

            $controller = $url_params[2];
            $function = $url_params[3];

            $http_method = $_SERVER['REQUEST_METHOD'];

            $function = $function . "_" . $http_method;


            $req_params = new \stdClass();
            $req_params->url = $url_params;
            $req_params->body = file_get_contents('php://input');


            if (method_exists($this, $function))
            {
                return $this->$function($req_params);
            }
            else
            {
                throw new EtsyException("Method does not exist in API");
            }
        }
        catch(Exception $e)
        {
            return $this->_output_exception($e);
        }
    }


    public function _add_no_cache_headers()
    {
        $this->output->set_header("HTTP/1.0 200 OK");
        $this->output->set_header("HTTP/1.1 200 OK");
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }


    public function print_json($content)
    {
        $json_content = json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        // $json_content = utf8_encode($json_content);


        $this->output->set_header("Content-Type: application/json; charset=utf-8");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Allow-Headers: Apikey");
        $this->output->set_header("Access-Control-Expose-Headers: Access-Control-Allow-Origin");
        $this->output->set_output($json_content);
    }


    public function getInputData($field_name, $input_array, $default_value = "")
    {
        if (! is_array($input_array))
        {
            $value = $default_value;
        }
        else
        {
            if (array_key_exists($field_name, $input_array))
            {
                $value = $input_array[$field_name];
            }
            else
            {
                $value = $default_value;
            }
        }
        // removing spaces at beg and end..
        $value = ltrim(rtrim($value));

        return $value;
    }





    public function check_mandatory($post_params, $mandatory_params)
    {
        $default_result = array('success' => 1);
        $param_validation = $this->utils->check_mandatory($post_params, $mandatory_params);

        // if validations fails, return error
        if (! $param_validation['success'])
        {
            // Commenting out as clients are already using meta->field_missing
            // and that's not in exception as such


            $debug_log = array('input_data' => $post_params);

            $meta_data = array();
            if (! empty($param_validation['missing']))
            {
                $meta_data['fields_missing'] = $param_validation['missing'];
            }

            throw new EtsyException("Mandatory Fields Missing");
        }

        return TRUE;
    }



    public function checkExistenceInRequestParamsAndAddDataToArray($key, $request_keys, $key_value, &$data_array)
    {
        if (array_search($key, $request_keys))
        {

            $data_array[$key] = $key_value;
        }
    }





}
