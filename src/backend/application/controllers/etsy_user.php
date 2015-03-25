<?php
if (! defined ( 'BASEPATH' ))
{
	exit ( 'No direct script access allowed' );
}

include_once 'application/models/container/user_info.php';

/**
 *
 *
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
		parent::__construct ();

		$this->load->model ( 'users_Model' );
		$this->load->model ( 'convos_Model' );
	}


	public function rest_front_controller()
	{
		try
		{
			$this->timer->start ();
			$headers = $this->input->request_headers ();

			// grabbing all url friendly params ie: /id/12/name/john
			$url_params = $this->uri->segment_array ();
			$num_params = count ( $url_params );

			// $http_method = $_SERVER['REQUEST_METHOD'];
			$http_method = strtoupper ( $this->_detect_method () );

			$req_params = new \stdClass ();
			$req_params->url = $url_params;
			$req_params->body = file_get_contents ( 'php://input' );

			if ($http_method == "GET")
			{
				$params = $this->input->get ();

				$recipient_id = Utils::getDataFromArray ( 3, $url_params, "" );
				$child_operation = Utils::getDataFromArray ( 4, $url_params, "" );

				if ($child_operation == "convos")
				{

					return $this->getUserConvos ( $recipient_id, $params );
				}
				else
				{
					return $this->getById ( $recipient_id );
				}
			}
			else
			{
				throw new EtsyException ( "Http Method Not supported yet" );
			}
		}
		catch ( Exception $e )
		{
			return $this->_output_exception ( $e );
		}
	}


	private function getUserConvos($recipient_id, $params)
	{
		$start_row = Utils::getDataFromArray ( "start_row", $params, 0 );
		$num_items = Utils::getDataFromArray ( "num_items", $params, 10 );
		$get_sent = Utils::getDataFromArray ( "get_sent", $params, 10 );
		$read_status = Utils::getDataFromArray ( "status", $params, "" );

		$convos_result = $this->convos_Model->getByUserId ( $recipient_id, $start_row, $num_items, $get_sent, $read_status );

		if ($convos_result)
		{
			$response ['success'] = 1;
			$response ['data'] = $convos_result;
			$this->print_json ( $response );
		}
		else
		{
			throw new EtsyException ( "No Convos Found" );
		}
	}


	public function getById($user_id)
	{
		$user_result = $this->users_Model->getById ( $user_id );

		if (! $user_result)
		{
			throw new EtsyException ( "User Not Found" );
		}
		else
		{
			$response ['success'] = 1;
			$response ['data'] = array ("user" => $user_result );
			$this->print_json ( $response );
		}
	}
}
