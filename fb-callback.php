<?php
if(!session_id()) {
    session_start();
}
 
ini_set('display_errors', 1);
error_reporting(~0);
require_once __DIR__ . '/vendor/facebook/graph-sdk/src/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '', // Replace {app-id} with your app id
  'app_secret' => '',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();
$_SESSION['FBRLH_state']=$_GET['state'];
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}

 

$_SESSION['fb_access_token'] = (string) $accessToken;

 try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('me?fields=picture.width(300),name', $accessToken->getValue());
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$graphEdge = $response->getGraphNode();
 $object = $response->getGraphObject();
$pic = $object->asArray('height');



$gen=uniqid();

copy($pic['picture']['url'], '/srv/http/fbapp2/'.$gen.'.jpg');
  
 
$skills=array("","Networking","Reverse","Exploit Master","Zer0Day-Hunter");
// Create Image From Existing File
$jpg_image = imagecreatefromjpeg('temp2.jpg');
$mypro=imagecreatefromjpeg($gen.'.jpg');
// Allocate A Color For The Text
$white = imagecolorallocate($jpg_image, 255, 255, 255);

// Set Path to Font File
$font_path = '/srv/http/fbapp2/font.ttf';

// Print Text On Image
imagettftext($jpg_image, 22, 0, 230, 430, $white, $font_path, $object->getProperty('name'));
imagettftext($jpg_image, 22, 0, 235, 460, $white, $font_path, rand(1, 1000));
imagettftext($jpg_image, 18, 0, 240, 500, $white, $font_path, $skills[rand(1,5)]);
// Output and free memory
ob_start (); 
// Copy and merge
imagecopymerge($jpg_image, $mypro, 245, 75, 0, 0, 320, 320, 75);

imagejpeg ($jpg_image);
$image_data = ob_get_contents (); 

ob_end_clean (); 

$image_data_base64 = base64_encode ($image_data);

// Send Image to Browser
echo "<img src='data:image/jpeg;base64,$image_data_base64'>";

file_put_contents($gen.'.jpg',base64_decode($image_data_base64));
$url__='http://localhost/fbapp2/'.$gen.'.jpg';


try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->post('/me/photos',array( 'message' => 'sss assignment test.','source' => $fb->fileToUpload($url__)), $accessToken->getValue());
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$graphNode = $response->getGraphNode();

echo 'Photo ID: ' . $graphNode['id'];




?>


 
