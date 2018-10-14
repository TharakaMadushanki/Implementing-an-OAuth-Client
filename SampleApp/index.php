<html>
<head>
	<meta charset="UTF-8">
	<title>Sample Application for Implementing OAuth Client</title>
	<link rel="stylesheet"  href="./public/styles/style.css">
	<style type="text/css">     
     .link{
        background-image: url("click.png");
        background-size: 400px 120px;
		width: 400px;
		height:120px;
		display:block;
		background-repeat: no-repeat;
		position:relative;
		margin-top:300px;
	  }
    </style>
	
    <script>var hidden = false;
		var count = 1;
		setInterval(function(){ // This function is here for the blink effect of the button
	
		document.getElementById("link").style.visibility= hidden ? "visible" : "hidden"; 
  
		hidden = !hidden;

		},300);
	</script>

</head>
<body>	
		<div class="login">
			<h1>Welcome to Sample Application</h1>
		</div>
		<div class="footer">
			<p>Implementing OAuth Client |  Tharaka Liyanage  |  IT13015886</p>
		</div>
</body>
</html>

<?php
// new 
session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '700188163695133',
  'app_secret' => '3c8b0f22eacad54c643fa3e4fdaa50b8',
  'default_graph_version' => 'v2.9',
  ]);
$helper = $fb->getRedirectLoginHelper();
//$permissions = ['email']; // optional
//$permissions = ['friendlist'];
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
		header('Location: http://localhost/SampleApp/data.php');
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
	//header('Location: http://localhost/fb/data.php');

} else {
	// replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	$loginUrl = $helper->getLoginUrl('http://localhost/SampleApp/index.php', $permissions);
	
	echo '<center><a class="link" href="' . $loginUrl . '"></a></center>';
}

?>