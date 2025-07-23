<?php
require __DIR__ . '/Session.php';
require __DIR__ . '/Captcha.php';

ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', 'Off');
ini_set('session.cookie_httponly', 'Off');

// Initialize Session
session_cache_limiter();
session_start();

/**
 * Refactor without Slim
 */

$visualCaptcha_session = new \visualCaptcha\Session();

$url_arr = explode('?r=', $_SERVER['REQUEST_URI']);
$request_page = $url_arr[0];

//for loading visualCaptcha language texts
$LANG = $_COOKIE['LANG'] ?? 'en';
$assetsPath = null;
$filePath = 'js/images_'.$LANG.'.json';
$defaultImages = json_decode( file_get_contents( $filePath ), true ); 

//start request
if (substr_count($request_page, 'start')) {
	$captcha = new \visualCaptcha\Captcha( $visualCaptcha_session, $assetsPath, $defaultImages );
	//$howmany = explode('start/', $request_page)[1] ?? 5;
	//$captcha->generate( $args['howmany'] ); //not secure to leave this in url params
	$captcha->generate( 5 ); //5 hardcoded
	echo json_encode( $captcha->getFrontEndData() );
}
//image request
elseif (substr_count($request_page, 'image')) {
    $captcha = new \visualCaptcha\Captcha( $visualCaptcha_session, $assetsPath, $defaultImages);
	$index = explode('image/', $request_page)[1];
	$index = explode('?', $index)[0];
    $retina = substr_count($request_page, 'retina=1') ? true : false;
    $headers = array();
    /**
     * Problem with setting headers on the fly
     * So we set headers here before captcha output something
     */
    set_Headers('image/png');
    $captcha->streamImage($headers, $index, $retina);
}
//audio request
elseif (substr_count($request_page, 'audio')) {
    /* not want audio
    $captcha = new \visualCaptcha\Captcha($visualCaptcha_session);
	$type = explode('type/', $request_page)[1] ?? 'mp3';
    $headers = array();
    set_Headers('audio/mpeg3');
    $captcha->streamAudio($headers, $type);	*/
}
//audio request
elseif (substr_count($request_page, 'try')) {
    $captcha = new \visualCaptcha\Captcha( $visualCaptcha_session );
    $frontendData = $captcha->getFrontendData();
    $params = Array();

    // Load the namespace into url params, if set
    if ( $namespace = $req->getParam( 'namespace' ) ) {
        $params[] =  'namespace=' . $namespace;
    }

    if (!$frontendData) {
        $params[] = 'status=noCaptcha';
    }
    else {
        // If an image field name was submitted, try to validate it
        if ( $imageAnswer = $req->getParam( $frontendData[ 'imageFieldName' ] ) ) {
            if ( $captcha->validateImage( $imageAnswer ) ) {
                $params[] = 'status=validImage';
            } else {
                $params[] = 'status=failure';
            }
        } 
		/* no audio
		else if ( $audioAnswer = $req->getParam( $frontendData[ 'audioFieldName' ] ) ) {
            if ( $captcha->validateAudio( $audioAnswer ) ) {
                $params[] = 'status=validAudio';
            } else {
                $params[] = 'status=failedAudio';
            }
        }*/
		else {
            $params[] = 'status=failedPost';
        }
        //$howMany = count( $captcha->getImageOptions() );
        //$captcha->generate( $howMany );
        $captcha->generate( 5 );//hardcoded for security
    }

    $app->redirect( '/login.php?' . join( '&', $params ) );
}

function set_Headers($mimeType) {
    // Set the appropriate mime type
    header('Content-Type: '.$mimeType);
    // Make sure this is not cached
    header('Cache-Control:no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}
?>