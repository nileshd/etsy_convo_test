<?php
if (! defined ( 'BASEPATH' ))
{
	exit ( 'No direct script access allowed' );
}

include_once 'application/models/container/user_info.php';
include_once 'application/models/container/convo_info.php';

/**
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 * Controller which handle Etsy Convos
 *
 * @author Nilesh Dosooye
 * @version 1.0
 * @name Etsy_convos
 * @access public
 *
 */
class Etsy_convo extends MY_Controller
{
	private $routes;


	public function __construct()
	{
		parent::__construct ();

		$this->load->model ( 'users_Model' );
		$this->load->model ( 'convos_Model' );

		// rest name to api name route
		$routes = array ();
		$routes [''] = "";
		$routes [''] = "";
		$routes [''] = "";
		$routes [''] = "";

		$this->routes = $routes;
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

			$params = new \stdClass ();
			$params->url = $url_params;
			parse_str ( file_get_contents ( 'php://input' ), $params->body );
			$id = Utils::getDataFromArray ( 3, $url_params );

			if ($http_method == "DELETE")
			{
				return $this->delete ( $id, $params );
			}
			else if ($http_method == "PUT")
			{
				$this->updateById ( $id, $params );
			}
			else if ($http_method == "POST")
			{
				$this->add ( $params );
			}
			else if ($http_method == "GET")
			{
				$params = $this->input->get ();
				$url_child = Utils::getDataFromArray ( 4, $url_params );

				if ($url_child == "thread")
				{

					$this->getByThreadId ( $id, $params );
				}
				else
				{

					$this->getById ( $id, $params );
				}
			}
			else
			{
				throw new EtsyException ( "UnSupported Http Method" );
			}
		}
		catch ( Exception $e )
		{
			return $this->_output_exception ( $e );
		}
	}


	public function getById($id, $params)
	{
		$convo_results = $this->convos_Model->getById ( $id );

		if (! $convo_results)
		{
			throw new EtsyException ( "Convo not found " );
		}
		else
		{
			$response ['success'] = 1;
			$response ['data'] = array ("convo" => $convo_results );
			$this->print_json ( $response );
		}
	}


	public function getByThreadId($id, $params)
	{
		$start_row = Utils::getDataFromArray ( "start_row", $params, 0 );
		$num_items = Utils::getDataFromArray ( "num_items", $params, 10 );

		$convo_results = $this->convos_Model->getByThreadId ( $id, $start_row, $num_items );

		$response ['success'] = 1;
		$response ['data'] = array ("convo_thread" => $convo_results );
		$this->print_json ( $response );
	}


	public function delete($id, $params)
	{
		$sender_id = Utils::getDataFromArray ( "sender_id", $params->body, null );

		$convo = $this->convos_Model->getById ( $id );

		if ($convo)
		{

			if (($sender_id != null) && ($sender_id != $convo->sender_id))
			{
				throw new EtsyException ( "you need to have written the message to be able to delete it." );
			}
			else
			{

				$delete_result = $this->convos_Model->delete ( $id );

				if (! $delete_result)
				{
					throw new EtsyException ( "Could not Delete Conversation Thread. Try again" );
				}
				else
				{
					$response ['success'] = 1;
					$response ['data'] = array ("deleted" => 1 );
					$this->print_json ( $response );
				}
			}
		}
		else
		{
			throw new EtsyException ( "Could not Find this Convo Thread." );
		}
	}


	public function updateById($id, $params)
	{
		$subject = Utils::getDataFromArray ( "subject", $params->body, null );
		$body = Utils::getDataFromArray ( "body", $params->body, null );
		$sender_id = Utils::getDataFromArray ( "sender_id", $params->body, null );
		$read_status = Utils::getDataFromArray ( "read_status", $params->body, null );

		$convo = $this->convos_Model->getById ( $id );

		if ($convo)
		{

			if ($sender_id != $convo->sender_id)
			{
				throw new EtsyException ( "you need to have written the message to be able to edit it." );
			}
			else
			{

				if (($subject !== "") || ($body != "") || ($read_status != ""))
				{
					$update_array = array ();
					if ($subject != "")
					{
						$update_array ['subject'] = $subject;
					}

					if ($body != "")
					{

						$update_array ['body'] = $body;
					}

					if ($read_status != "")
					{

						$update_array ['status'] = $read_status;
					}

					$where_array = array ("id" => $id );

					$update_results = $this->convos_Model->updateByFK ( $update_array, $where_array );

					if ($update_results)
					{

						$response ['success'] = 1;
						$response ['data'] = array ("updated" => 1 );
						$this->print_json ( $response );
					}
				}
				else
				{
					throw new EtsyException ( "You did not pass any values to update. Values stayed the same." );
				}
			}
		}
		else
		{
			throw new EtsyException ( "Could not find conversation that you wanted to update." );
		}
	}


	public function add($params)
	{
		$sender_id = Utils::getDataFromArray ( "sender_id", $params->body, null );
		$recipient_id = Utils::getDataFromArray ( "recipient_id", $params->body, null );
		$subject = Utils::getDataFromArray ( "subject", $params->body, null );
		$body = Utils::getDataFromArray ( "body", $params->body, null );
		$reply_convo_id = Utils::getDataFromArray ( "reply_convo_id", $params->body, null );
		$root_parent_id = Utils::getDataFromArray ( "root_parent_id", $params->body, null );

		$convo_info = new ConvoInfo ();
		$convo_info->sender_id = $sender_id;
		$convo_info->recipient_id = $recipient_id;
		$convo_info->subject = $subject;
		$convo_info->body = $body;

		if ($reply_convo_id != null)
		{
			$convo_info->parent_id = $reply_convo_id;

			if ($root_parent_id == null)
			{
				throw new EtsyException ( "You need to also pass in the Root Parent Id if you are adding a child" );
			}
			else
			{
				$convo_info->root_parent_id = $root_parent_id;
			}
		}

		$convo_id = $this->convos_Model->add_convo ( $convo_info );

		if ($convo_id)
		{
			$response ['success'] = 1;
			$response ['data'] = array ("convo_id" => $convo_id );
			$this->print_json ( $response );
		}
		else
		{
			throw new EtsyException ( "Sorry, Error. Could not add new convo." );
		}
	}
}
