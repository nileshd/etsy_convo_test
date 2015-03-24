<?php
/*
*	Copyright © GottaPark Inc. 2007-2014 All Rights Reserved
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Timer
{
  
  private $start;
  private $pause_time;

  /*  start the timer  */
  public function __construct($params = array(false)) {
    if($params[0]) { $this->start(); }
  }

  /*  start the timer  */
  public function start() {
    $this->start = $this->get_time();
    $this->pause_time = 0;
  }

  /*  pause the timer  */
  public function pause() {
    $this->pause_time = $this->get_time();
  }

  /*  unpause the timer  */
  public function unpause() {
    $this->start += ($this->get_time() - $this->pause_time);
    $this->pause_time = 0;
  }

  /*  get the current timer value  */
  public function get($decimals = 8) {
    return round(($this->get_time() - $this->start),$decimals);
  }

  /*  format the time in seconds  */
  public function get_time() {
    list($usec,$sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
  }
}

/*


$timer = new timer(true); // constructor starts the timer, so no need to do it ourselves

$query_time = $timer->get();

$processing_time = $timer->get();

*/