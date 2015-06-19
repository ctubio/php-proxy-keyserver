<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Request;

class Peer {

  public static $stats_instance = NULL;

  public static function getStats($hostname) {
    if (self::$stats_instance === NULL)
      self::$stats_instance = self::get(
      $hostname, '/pks/lookup?op=stats'
    );
    return self::$stats_instance;
  }

  public static function validate($line) {
    $line = self::tab2space($line);
    echo '<small>Checking line..<br />';
    if (substr_count($line, '#')===0)
      return self::warn('format', 'the server and/or contact part/s doesn\'t exists.</span><br /><br /><span class="uid" style="text-decoration:none;">But it was fun to parse.</span><br /><br /><span>Please, next time submit a real membership line.');
    if (substr_count($line, '#')!==1)
      return self::warn('format', 'the symbol # appears more than once.');
    list($server, $contact) = explode('#', $line);
    $server = trim($server);
    $contact = trim($contact);
    echo 'Checking keyserver..<br />';
    if (!$server || !$contact)
      return self::warn('format', 'the server or the contact part is missing.');
    if (substr_count($server, ' ')!==1)
      return self::warn('format', 'the server address part contains unknown data.');
    list($hostname, $port) = explode(' ', $server);
    if (!is_numeric($port))
      return self::warn('format', 'the server port is not numeric.');
    echo 'Checking contact..<br />';
    if (substr_count($contact, '<')!==1 || substr_count($contact, '>')!==1)
      return self::warn('format', 'the contact part contains unknown data.');
    list($contact, $key) = explode('>', $contact);
    $key = trim($key);
    if (!$key)
      return self::warn('format', 'the contact key part is missing.');
    list($name, $mail) = explode('<', $contact);
    $name = trim($name);
    if (!self::validateKey($key, $mail))
      return self::warn('format', 'the contact mail is not within the key.');
    if (!self::validateContact($hostname, $key))
      return self::warn('contact', NULL, $key.' was not found as Server contact of '.$hostname);
    if (!self::validateServer($hostname))
      return self::warn('server', '<br /><br /><span class="uid" style="text-decoration:none;">Thank you '.strtok($name, ' ').'!', $hostname);
    if (!self::missingLine($hostname))
      return self::warn('exists', '<span class="uid" style="text-decoration:none;">Thank you '.strtok($name, ' ').'!', $hostname);
    if (!self::save($line))
      return self::warn('admin', NULL, $hostname);
    else return '<span class="uid" style="text-decoration:none;">Your line was added successfully to the membership file of '.Keyserver::getConfig()->hostname.'.<br /><br />The <a href="/pks/lookup?op=stats">stats</a> page will be refreshed soon with your changes.</span><br /><br /><span class="uid" style="text-decoration:none;">Thank you '.strtok($name, ' ').'!</span>';
  }

  public static function validateKey($key, $mail) {
    echo 'Checking '.$mail.'..<br />';
    return (strpos(Factory::forward(Request::create('/pks/lookup?search='.$key.'&op=vindex'))->to(
      'http://'.Keyserver::getConfig()->hkp_load_balanced_addr.':'.(Keyserver::getConfig()->hkp_load_balanced_port ?: Keyserver::getConfig()->hkp_public_port).'/pks/lookup?search='.$key.'&op=vindex'
    )->getContent(), $mail)!==FALSE);
  }

  public static function validateServer($hostname) {
    echo 'Checking '.Keyserver::getConfig()->hostname.' in '.$hostname.'..<br />';
    return (strpos(self::getStats($hostname), '<td>'.Keyserver::getConfig()->hostname.' 11370</td>')!==FALSE);
  }

  public static function validateContact($hostname, $key) {
    echo 'Checking '.$key.'..<br />';
    return (strpos(self::getStats($hostname), '<td>'.$key.'</td>')!==FALSE);
  }

  public static function missingLine($hostname) {
    echo 'Checking membership file..<br />';
    $file = '/var/lib/sks/membership';
    if (!file_exists($file) || !is_readable($file)) return TRUE;
    $file = file_get_contents($file);
    return (strpos($file, $hostname)===FALSE);
  }

  public static function save($line) {
    echo 'Saving membership..<br />';
    $file = '/var/lib/sks/membership';
    if (!file_exists($file) || !is_readable($file) || !is_writable($file)) return FALSE;
    file_put_contents($file, (file_get_contents($file).PHP_EOL.$line));
    return (strpos(file_get_contents($file), $line)!==FALSE);
  }

  public static function warn($scope, $msg = NULL, $subject = NULL) {
    return '</small><br /><span class="warn">'.strtr($scope, array(
      'contact' => 'Your membership line is correct, but after checking your stats page, '.$subject.' (or the page was unreachable).',
      'format' => 'Membership line mal-formed, ',
      'exists' => 'Your membership line was already added to '.Keyserver::getConfig()->hostname.'.</span><br /><br />',
      'admin' => 'Your membership line is correct, and '.Keyserver::getConfig()->hostname.' was found as a Gossip Peer in the stats page of '.$subject.', but an error occurred while saving your membership line, please send an email to <a href="mailto:'.Keyserver::getConfig()->bugs_contact_mail.'">&lt;'.Keyserver::getConfig()->bugs_contact_mail.'&gt;</a> as usual.',
      'server' => 'Your membership line is correct, but after checking the stats page of '.$subject.', '.Keyserver::getConfig()->hostname.' was not found as a Gossip Peer.</span><br /><br /><span class="uid" style="text-decoration:none;">Please, add the following line to your membership file and wait until it is refreshed before continue:</span><br /><br /><input style="width:100%;padding:5px;;text-align:center;font-weight:600;" value="'.htmlentities(Keyserver::getConfig()->membership_line).'" />'
    )).$msg.'</span><br />';
  }

  public static function tab2space($line, $tab = 1, $nbsp = FALSE) {
    while (($t = mb_strpos($line,"\t")) !== FALSE) {
        $preTab = $t?mb_substr($line, 0, $t):'';
        $line = $preTab . str_repeat($nbsp?chr(7):' ', $tab-(mb_strlen($preTab)%$tab)) . mb_substr($line, $t+1);
    }
    return  preg_replace('!\s+!', ' ', $nbsp?str_replace($nbsp?chr(7):' ', '&nbsp;', $line):$line);
  }

  public static function getContent($url) {
    $content = NULL;
    $info = array();
    if (function_exists('curl_init')) {
      $ch = curl_init();
      curl_setopt ($ch, CURLOPT_URL, $url);
      curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      $content = curl_exec($ch);
      $info = ($content) ? curl_getinfo($ch) : array();
      curl_close($ch);
    }
    return ($content && isset($info['http_code']) && $info['http_code']===200)
      ? $content : NULL;
  }

  public static function get($hostname, $url) {
    echo 'Checking https://'.$hostname.$url.'..<br />';
    $content = self::getContent('https://'.$hostname.$url);
    if (!$content) {
      echo 'Checking http://'.$hostname.':11371'.$url.'..<br />';
      $content = self::getContent('http://'.$hostname.':11371'.$url);
    }
    if (!$content) {
      echo 'Checking http://'.$hostname.$url.'..<br />';
      $content = self::getContent('http://'.$hostname.$url);
    }
    if (!$content) echo '<span class="warn">Unreachable stats page, please double-check the hostname.</span><br /><br />';
    return $content;
  }
}
