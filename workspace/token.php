<?php

require "../phplib/genlibraries.php";
//require      __DIR__ . '/../vendor/league_oauth2-client.ORI/vendor/autoload.php';
require      __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Client\Provider\GenericProvider;

// Setting auth server
$provider = new GenericProvider([
    'clientId'                => 'mug',                                  // vre client id for auth server
    'clientSecret'            => '6808c5e1-7e88-4d6d-a325-6f8184a18ff0', // vre client key. Only if client is "confidential". Found in Client->Installation->json
    'redirectUri'             => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],               // callback
    'urlAuthorize'            => 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/auth',    // auth openID endpoint
    'urlAccessToken'          => 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/token',   // token openID endpoint
    'urlResourceOwnerDetails' => 'https://inb.bsc.es/auth/realms/mug/protocol/openid-connect/userinfo' // onwer openId endpoint
//    'scope'   => 'offline_access',
]);


// Check if user has already accesstoken
//if (isset($_SESSION['User']) && isset($_SESSION['User']['token']) ){
	//$acessToken_old = $_SESSION['User']['token'];

$accessToken;


//if(0){
if (isset($_SESSION['token']) ){
	$accessToken_old=unserialize(serialize($_SESSION['token']));
	print "<br>GETTING OLD TOKEN------------------<br>";
	var_dump($accessToken_old);

	if ($accessToken_old->hasExpired()) {
		print "<br>ACCESS TOKEN EXPIRED.<br/>";
		unset($_SESSION['token']);
		if (! $accessToken_old->hasExpiredRefresh() ){
			print "<br>REFRESH THEN ACTIVE. REFRESHING <br/>";
			$accessToken = $provider->getAccessToken('refresh_token', ['refresh_token' => $accessToken_old->getRefreshToken()]);
			if ($accessToken->getToken())
				$_SESSION['token'] = $accessToken;
		}
	}
}

if (!isset($_SESSION['token']) ){

	// Get auth code
	if (!isset($_GET['code'])) {

	    // Fetch the authorization URL from the provider; returns urlAuthorize and generates state
	    $authorizationUrl = $provider->getAuthorizationUrl();
	    // Get state generated for you and store it to the session.
	    $_SESSION['oauth2state'] = $provider->getState();

	    // Redirect the user to the authorization URL.
	    header('Location: ' . $authorizationUrl);
	    exit;
	
	// Check given state against previously stored one to mitigate CSRF attack
	} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
	
	    if (isset($_SESSION['oauth2state'])) {
	        unset($_SESSION['oauth2state']);
	    }
	    exit('Invalid state');
	

	} else {
	    try {
	        // Try to get an access token using the authorization code grant.

	print "\n<br/>CODE = ".$_GET['code']."\n<br/>";

	
	        $accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
		$_SESSION['token']= $accessToken;
	
		// Using the access token, we may look up details about the resource owner.
	        $resourceOwner  = $provider->getResourceOwner($accessToken);
		$resourceOwner2 = $resourceOwner->toArray();
		print "<br><br>getResourceOwner RETURNS <br/>";
	        var_export($resourceOwner->toArray());

		// Store token
		loadUser_oauth2($resourceOwner2['name'],$accessToken->getToken());
		
	
	    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
		 // Failed to get the access token or user details.
	        exit($e->getMessage());
	
	    }
	
	}
}


	
// The provider provides a way to get an authenticated API request for the service, using the access token; it returns an object conforming to Psr\Http\Message\RequestInterface.

$accessToken = unserialize(serialize($_SESSION['token']));

print "<br/>---------------------------TOKEN  -------------------<br/>";
       echo 'Access Token:   ' . $accessToken->getToken() . "<br>";
       echo 'Refresh Token:  ' . $accessToken->getRefreshToken() . "<br>";
       echo 'Expired in:     ' . $accessToken->getExpires() . "<br>";
       echo 'Already expired?' .($accessToken->hasExpired() ? 'expired' : 'not expired') . "<br>";
print "<br/>--------------------------- -------------------<br/>";


$resourceOwner  = $provider->getResourceOwner($accessToken);
print "<br><br>getResourceOwner RETURNS <br/>";
var_export($resourceOwner->toArray());


	
?>
