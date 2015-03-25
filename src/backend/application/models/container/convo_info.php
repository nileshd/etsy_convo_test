<?php

include_once 'application/models/container/info.php';


class ConvoInfo extends Info
{
    public $id;

    // Routing Info
    public $sender_id;
    public $recipient_id;

    // For Replies
    public $parent_id;
    public $root_parent_id;

    // Convo Body
    public $subject;
    public $body;
    public $status;
    public $date_created;
    public $date_read;


    function __construct()
    {
    	// Defaulting Convo to top of thread
    	$this->parent_id = 0;
    	$this->root_parent_id = 0;
    }


    function __destruct()
    {
    }

}
