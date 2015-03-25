<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

include_once 'application/models/container/convo_info.php';
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
        $this->_table = "convos";
    }


    public function getById($id)
    {
    	$obj = parent::getById($id);
    	return $obj;
    }




    public function getByThreadId($root_parent_id,$start_row=0,$num_items=10)
    {

    	if (!$this->isTopParent($root_parent_id))
    	{
    		throw new EtsyException("Can only get thread for a Root Top Parent Id");
    	}

    	$sql =<<<EOT
    	SELECT  c.id,c.subject,c.sender_id,c.recipient_id,c.body,c.parent_id,c.root_parent_id
        FROM convos c
        WHERE c.id = '{$root_parent_id}' OR  c.root_parent_id = '{$root_parent_id}'
        GROUP BY c.id
    	LIMIT $start_row , $num_items
EOT;

    	$query_results = $this->db->query($sql);


    	$convos = array();
    	if ($query_results->num_rows() > 0)
    	{
    		foreach ($query_results->result() as $row)
    		{

    		        $convo = array();

    				$convo['id'] = $row->id;
    				$convo['sender_id'] = $row->sender_id;
    				$convo['recipient_id'] = $row->recipient_id;


    				$convo['subject'] = $row->subject;
    				$convo['body'] = $row->body;

    				$convo['parent_id'] = $row->parent_id;
    				$convo['root_parent_id'] = $row->root_parent_id;


    				$convos[] = $convo;
    		}
    	}

    	return $convos;
    }


    public function getByUserId($user_id,$start_row=0,$num_items=10,$get_sent=false,$read_status="")
    {

    	$sql =<<<EOT
    	SELECT  c.id,c.subject,c.sender_id,c.recipient_id,
        (SELECT count(*) FROM convos WHERE root_parent_id = c.id ) as thread_count
        FROM convos c
        WHERE 1 = 1
EOT;

    	// If user need to get the sent email they have
    	if ($get_sent)
    	{
    		 $sql .= " AND c.sender_id = '{$user_id}' ";
    	}
    	else
    	{
    		$sql .= " AND c.recipient_id = '{$user_id}' ";
    	}

    	if ($read_status!="")
    	{
    		if (($read_status=="read") || ($read_status=="unread"))
    		{
    			$sql .= " AND c.status = '{$read_status}' ";
    		}
    	}



        $sql .= "  GROUP BY c.id  LIMIT $start_row , $num_items ";

    	$query_results = $this->db->query($sql);


    	$convos = array();
    	if ($query_results->num_rows() > 0)
    	{
    		foreach ($query_results->result() as $row)
    		{

    		        $convo = array();

    				$convo['id'] = $row->id;
    				$convo['sender_id'] = $row->sender_id;
    				$convo['recipient_id'] = $row->recipient_id;

    				$convo['subject'] = $row->subject;
    				$convo['thread_count'] = $row->thread_count;
    				$convos[] = $convo;
    		}
    	}

    	return $convos;
    }


    private function isChildFromParent($child_id,$parent_id)
    {

    	$sql = "SELECT id from convos where  id = '{$child_id}' and root_parent_id = '{$parent_id}' ";
    	$results = $this->db->query($sql);
    	if ($results->num_rows > 0)
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    private function isTopParent($parent_id)
    {

    	$sql = "SELECT id from convos where root_parent_id is null and id = {$parent_id} ";


    	$results = $this->db->query($sql);
    	if ($results->num_rows > 0)
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }


    public function add_convo(ConvoInfo $convo_info)
    {

    	$date_now = Utils::getDateTime();

    	$convo_info->status = "unread";

    	if (!$convo_info->parent_id) { $convo_info->parent_id = null; }
    	if (!$convo_info->root_parent_id) { $convo_info->root_parent_id = null; }

    	// Replies share the same subject as the parent.. so we don't need to save it here
    	if ($convo_info->parent_id) {

    		// No Replies for threads
    		$convo_info->subject = "";

    		if (!$this->isChildFromParent($convo_info->parent_id,$convo_info->root_parent_id))
    		{
    			throw new EtsyException("Root parent Id and Parent id don't seem to be related");
    		}

    	}

    	$this->db->trans_start();
    	$insert_values = array(

    			'sender_id' => $convo_info->sender_id,
    			'recipient_id' => $convo_info->recipient_id,
    			'subject' => $convo_info->subject,
    			'body' => $convo_info->body,
    			'parent_id' => $convo_info->parent_id,
    			'root_parent_id' => $convo_info->root_parent_id,
    			'date_created' => $date_now,
    			'status' => $convo_info->status,
         );

    	$insert_results = $this->db->insert($this->_table, $insert_values);

    	if ($insert_results === TRUE)
    	{
    		$new_user_id = $this->db->insert_id();

    		$this->db->trans_complete();
    		return $new_user_id;
    	}
    	else
    	{
    		throw new EtsyException("Convo could not be saved to Db :( Try again later.");
    	}
    }


    public function delete($convo_id)
    {


    	$this->db->trans_start();
        // Deleting Child Records... FK Are set up to be cascade on delete.. so children will be deleted too
    	$delete_children_results = $this->db->delete($this->_table, array('id' => $convo_id));

    	if ($delete_children_results)
    	{

    		$this->db->trans_complete();

    		return TRUE;
    	}
    }


}