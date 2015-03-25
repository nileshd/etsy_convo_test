<?php
if (! defined ( 'BASEPATH' ))
{
	exit ( 'No direct script access allowed' );
}

/**
 *
 *
 *
 * Controller which will help us to handle the pages
 *
 * @category Admin
 * @author Nilesh Dosooye
 * @version 1.0
 * @name Pages
 * @access public
 *
 */
class Pages extends MY_Controller
{
	private $page_title_default = "Etsy Test Docs";


	/**
	 * Default constructor which will construct the page object
	 *
	 * @author Nilesh Dosooye
	 * @name __construct
	 * @access public
	 * @return none
	 */
	public function __construct()
	{
		parent::__construct ();
	}


	public function usage()
	{
		$data = array ();

		$data ['page_title'] = $this->page_title_default;

		$this->load->view ( 'apidoc/template/header', $data );
		$this->load->view ( 'apidoc/pages/api_doc', $data );
		$this->load->view ( 'apidoc/template/footer', $data );
	}


	public function index()
	{
		$data = array ();
		$this->load->view ( 'apidoc/template/header', $data );
		$this->load->view ( 'apidoc/list_apis', $data );
		$this->load->view ( 'apidoc/template/footer', $data );
	}


	public function api_details($id)
	{
		$data = array ();
		$this->load->view ( 'apidoc/template/header', $data );
		$this->load->view ( 'apidoc/api_details', $data );
		$this->load->view ( 'apidoc/template/footer', $data );
	}


	public function user_ws($id)
	{
		$data = array ();
		$this->load->view ( 'apidoc/template/header', $data );
		$this->load->view ( 'apidoc/user_ws', $data );
		$this->load->view ( 'apidoc/template/footer', $data );
	}


	public function convos_ws($id)
	{
		$data = array ();
		$this->load->view ( 'apidoc/template/header', $data );
		$this->load->view ( 'apidoc/convos_ws', $data );
		$this->load->view ( 'apidoc/template/footer', $data );
	}
}