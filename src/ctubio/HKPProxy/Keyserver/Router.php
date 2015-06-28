<?php namespace ctubio\HKPProxy\Keyserver;

use ctubio\HKPProxy\Keyserver;
use ctubio\HKPProxy\Keyserver\Skin;
use Proxy\Factory;
use Symfony\Component\HttpFoundation\Response;

class Router {

  public static function getResponse() {
    $response = strpos($uri = self::fixFriendlyUrl(preg_replace('/^\/$/', '/index',
      Keyserver::getRequest()->server->get('REQUEST_URI')
    )), '/pks/') === 0
      ? Skin::parseContent(self::getHKPResponse($uri))
      : Skin::parsePhtml(new Response(), rtrim(strtok($uri,'?'),'/'));

    if (($errno = (int)$response->getStatusCode()) !== 200)
      $response = Skin::parsePhtml($response, '/errors/'.$errno);

    return $response;
  }

  public static function fixFriendlyUrl($uri) {
    if (!Keyserver::getConfig()->show_friendly_urls) return  $uri;
    Keyserver::getRequest()->server->set('ORIGINAL_REQUEST_URI', $uri);
    if (strpos($_uri=$uri,'/get/')===0 || strpos($uri,'/0x')===0 || strpos($uri,'/search/')===0 || strpos($uri,'/download/')===0) {
      $uri = explode('/',trim($uri,'/'));
      Keyserver::getRequest()->query->set('search', $uri = str_replace('+',' ',array_pop($uri)));
      $uri = (strpos($_uri,'/search/')===0
        ? '/pks/lookup?fingerprint=on&op=vindex&search='
        : (strpos($_uri,'/download/')===0
          ?'/pks/lookup?op=get&options=mr&search='
          : '/pks/lookup?op=get&search=')
      ).$uri;
    }

    Keyserver::getRequest()->server->set('REQUEST_URI', $uri);
    return $uri;
  }

  public static function getHKPResponse($uri) {
    $config = Keyserver::getConfig();

    try {
      return Factory::forward(Keyserver::getRequest())->to(
        'http://'.($addr=(($primary=($config->hkp_primary_keyserver_addr && (strpos($uri, '/pks/lookup?op=stats')===0 || strpos($uri, '/pks/hashquery')===0)))?$config->hkp_primary_keyserver_addr:$config->hkp_load_balanced_addr).':'.((!$primary && $config->hkp_load_balanced_port)?$config->hkp_load_balanced_port:$config->hkp_public_port)).$uri
      );
    } catch (\Exception $e) {
      return new Response(
        Log::catchError($e, 'Double-check if the keyserver is up and running at the expected address:port ('.$addr.').')
      );
    }
  }
}
