<?php

//namespace ExtendedGenericProvider;
namespace MuG_Oauth2Provider;

require  __DIR__ .'/../vendor/autoload.php';
use League\OAuth2\Client\Provider\GenericProvider;

class MuG_Oauth2Provider extends GenericProvider {

    protected $urlLogout;

    public function __construct(array $options = [], array $collaborators = [])
    {

        // set openID endpoints from global app conf
        if (!$options['urlAuthorize'] && $GLOBALS['urlAuthorize'])
             $options['urlAuthorize'] = $GLOBALS['urlAuthorize'];
        if (!$options['urlAccessToken'] && $GLOBALS['urlAccessToken'])
             $options['urlAccessToken'] = $GLOBALS['urlAccessToken'];
        if (!$options['urlResourceOwnerDetails'] && $GLOBALS['urlResourceOwnerDetails'])
             $options['urlResourceOwnerDetails'] = $GLOBALS['urlResourceOwnerDetails'];
        if (!$options['urlLogout'] && $GLOBALS['urlLogout'])
             $options['urlLogout'] = $GLOBALS['urlLogout'];
        
        // set VRE as openID client
        if (!$options['clientId'] && !$options['clientSecret']){
            $conf = getConf(__DIR__."/../../conf/oauth2.conf");
            if ($conf[0])
                $options['clientId']     = $conf[0];
            if ($conf[1])
                $options['clientSecret'] = $conf[1];
        }

        // add urlLogout property
        if ($options['urlLogout'])
            $this->urlLogout = $options['urlLogout'];

        parent::__construct($options, $collaborators);
    }

    public function logoutSession($refresh_token){

        if (!$refresh_token){
            //die("CACACACACA !!! a logoutSession() ja no li arriba la var session. Es tanca la php session abans!!");
            return true;
        }

        $post_data    = "refresh_token=$refresh_token";
        $headers      = array("Content-Type: application/x-www-form-urlencoded");
        $basic_auth   = array(  "user" => $this->clientId,
                                "pass" => $this->clientSecret
                        );
        #print "CMD: curl -v -X POST -H \"Content-Type: application/x-www-form-urlencoded\" --user $this->clientId:$this->clientSecret --data \"$post_data\" --url ".$this->urlLogout. "</br/>";
        list($resp,$info) =post($post_data,$GLOBALS['urlLogout'],$headers,$basic_auth);

        if ($info['http_code'] == 400){
            if ($resp){
                $err = json_decode($resp,TRUE);
                throw new Exception("Logout client session unauthorized. [".$err['error']."]: ".$err['error_description']);
            }else{
                throw new Exception("Logout client session unauthorized.");
            }
        }
        return true;
    }

}
