<?php
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook([
  'app_id' => '700188163695133',
  'app_secret' => '3c8b0f22eacad54c643fa3e4fdaa50b8',
  'default_graph_version' => 'v2.9',
  ]);
$helper = $fb->getRedirectLoginHelper();

$permissions =  array("email","user_friends");	
try {
	if (isset($_SESSION['facebook_access_token'])) {
		$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	// When Graph returns an error
 	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
 	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
 }
if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		// getting short-lived access token
		$_SESSION['facebook_access_token'] = (string) $accessToken;
	  	// OAuth 2.0 client handler
		$oAuth2Client = $fb->getOAuth2Client();
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		// setting default access token to be used in script
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	// redirect the user back to the same page if it has "code" GET variable
	if (isset($_GET['code'])) {
		header('Location: ./');
	}
    
    
    // Getting user facebook profile info
    try {
 
        $profileRequest = $fb->get('/me?fields=email',$_SESSION['facebook_access_token']);
        $profileRequest1 = $fb->get('/me?fields=name');
        $requestPicture = $fb->get('/me/picture?redirect=false&height=310&width=300'); //getting user picture        
        $requestFriends = $fb->get('/me/taggable_friends?fields=name&limit=20');
		$fbUserProfile = $profileRequest->getGraphNode()->asArray();
		$friends = $requestFriends->getGraphEdge();
		
		$a = $fb->get('/me/friends?fields=name,gender');
		$b = $a ->getGraphEdge();
		$fbUserEmail = $profileRequest->getGraphNode();
        $fbUserProfile1 = $profileRequest1->getGraphNode();
        $picture = $requestPicture->getGraphNode(); 		    
    }
    catch(FacebookResponseException $e) {
    
    	
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
   
   
  $output1 = json_encode($fbUserProfile1['name']);
  $output2 = json_encode($fbUserEmail['email']);
  
   
}else{

}
?>
<html>
<head>
<title>Facebook app</title>
	<meta charset="UTF-8">
	<title>Sample Application for Implementing OAuth Client</title>
	<link rel="stylesheet"  href="./public/styles/style.css">
	<script src="html2canvas.js"></script> 
<style type="text/css">

    .loader{
    
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 1s linear 3;
    animation: spin 1s linear 3;
    position:relative;
    top:130px;
    left:350px;
       
    }
    
    @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }

    </style>
    <script>
    var hidden = false;


setTimeout(function(){


document.getElementById("you").style.visibility='hidden';
document.getElementById("content").style.visibility='hidden';
},1);


setTimeout(function(){


document.getElementById("you").style.visibility='visible';
document.getElementById("content").style.visibility='visible';
},3000);



</script>

 
</head>
<body>
		<div class="profile" align="center" color="white">
			<h1>Tharaka's Profile Info</h1>
		</div>
		<div class="profile" align="middle">
			<?php echo 
		"<img src='".$picture['url']."' class='you' id='you' /><p class='content' id='content' style='color:white;'></p>
		<p>Profile Name: $output1</p><p>Email: $output2</p>"; ?>		
		</div>
		<div class="footer">
			<p>Implementing OAuth Client |  Tharaka Liyanage  |  IT13015886</p>
		</div>

    
    </body>
</html>
