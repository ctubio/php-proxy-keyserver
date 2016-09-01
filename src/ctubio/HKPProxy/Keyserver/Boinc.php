<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;

class Boinc {

  private $host = array();

  public function __construct() {
    foreach(explode(',',Keyserver::getConfig()->boinc_machines_addr) as $host) {
      $host = trim($host);
      $this->host[$host] = $this->getState($host);
    }
  }

  public function __toString() {
    return $this->host ? implode(NULL, $this->host) : NULL;
  }

  private function getState($host) {
    $fp = fsockopen ($host, (int)Keyserver::getConfig()->boinc_machines_port, $errno, $errstr, 30);
    if (!$fp) return 'Server down.';

    $request=sprintf("<boinc_gui_rpc_request>\n%s\n</boinc_gui_rpc_request>\n\003",'<get_state/>');
    fputs ($fp, $request);
    $res = NULL;
    while (!feof($fp)) {
      $char = fgetc($fp);
      if (($char===false) || ($char=="\003")) break;
      $res .= $char;
    }
    fclose($fp);

    try {
      $xml = new \SimpleXMLElement($res);
    } catch (\Exception $ex) {
      return 'Service down.';
    }

    $host = $xml->xpath('/boinc_gui_rpc_reply/client_state/host_info');
    $return = '<h4>'.$host[0]->p_ncpus.'x '.$host[0]->p_model.':</h4>';

    $result = $xml->xpath('/boinc_gui_rpc_reply/client_state/result');
    while(list( , $node) = each($result)) {
      $class='state-done';
      if ($node->active_task) {
        if ($node->active_task->active_task_state=='1') $class='state-active';
        else $class='state-pause';
      }
      else if ($node->state==2) $class='state-queue';

      $return .= '<table style="margin:0px auto;" class='.$class.'>'
        .'<tr><td width="150">Project: </td><td width="450">'.current(array_slice(explode('/',$node->project_url),2,1)).'</td></tr>'
        .'<tr><td width="150">Task: </td><td width="450">'.$node->name.'</td></tr>'
        .'<tr><td>State: </td><td>'.strtr($node->state,array('1'=>'Completed, transferring..','5'=>'Completed, waiting for validation','2'=>$node->active_task?($node->active_task->active_task_state=='1'?'In progress':'Paused'):'Queued')).'</td></tr>'
        .'<tr><td>Received: </td><td>'.$this->timestampToStr($node->received_time).'</td></tr>'
        .'<tr><td>Deadline: </td><td>'.$this->timestampToStr($node->report_deadline).'</td></tr>';
      if ($node->active_task) {
        $return .= '<tr><td>CPU time remaining: </td><td>'.$this->secToStr($node->estimated_cpu_time_remaining).'</td></tr>'
        .'<tr><td>Elapsed CPU time: </td><td>'.$this->secToStr($node->active_task->current_cpu_time).'</td></tr>'
        .'<tr><td>Slot: </td><td>'.$node->active_task->slot.'</td></tr>'
        .'<tr><td>Fraction done: </td><td>'.number_format(floatval($node->active_task->fraction_done)*100,1).'%'.'</td></tr>';
      } else if (!in_array($node->state, array(1,2))) {
        $return .= '<tr><td>Complete: </td><td>'.$this->timestampToStr($node->completed_time).'</td></tr>'
        .'<tr><td>Final CPU time: </td><td>'.$this->secToStr($node->final_cpu_time).'</td></tr>'
        .'<tr><td>Total elapsed time: </td><td>'.$this->secToStr($node->final_elapsed_time).'</td></tr>';
      }
      $return .= '</table><br />'/*.'<!--'.PHP_EOL.print_r($node->asXML(),true).PHP_EOL.'-->'*/;
    }
    return $return;
  }

  private function timestampToStr($t) {
    $t = intval($t);
    if ($t<=0) return '';
    return date('Y-m-d H:i', $t);
  }

  private function secToStr($t) {
    if ($t<=0) return '';
    $t = intval($t);
    $d = floor($t/(60*60*24));
    $h = floor(($t-($d*60*60*24))/(60*60));
    $m = floor(($t-($d*60*60*24)-($h*60*60))/(60));
    return sprintf('%d days, %d hours, %d minutes', $d, $h, $m);
  }
}
