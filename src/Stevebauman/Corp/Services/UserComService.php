<?php

namespace Stevebauman\Corp\Services;

use COM;
use Illuminate\Support\Facades\Config;
use Stevebauman\Corp\Facades\Corp;

class UserComService {
    
    /*
     * Holds the COM object
     */
    private $com;
    
    /*
     * Holds the server name
     */
    private $server = '';
    
    /*
     * Holds the administrator username
     */
    private $adminUser = '';
    
    /*
     * Holds the administrator password
     */
    private $adminPassword = '';
    
    public function __construct()
    {   
        /*
         * Construct a new COM object
         */
        $this->com = new COM("LDAP:");

        /*
         * Get configuration details
         */
        $this->server           = Config::get('corp::adldap_config.domain_controllers.0');
        $this->adminUser        = Config::get('corp::adldap_config.admin_username');
        $this->adminPassword    = Config::get('corp::adldap_config.admin_password');
    }
    
    /**
     * Sets an LDAP user password using COM
     * 
     * @param type $password
     * @return boolean
     */
    public function password($username, $password)
    {
        /*
         * Get the user
         */
        $corpUser = $this->getUser($username);
        
        /*
         * Get the distiguished name from the user object
         */
        $userDn = $corpUser->dn_string;
        
        /*
         * Get the DS object
         */
        $user = $this->com->OpenDSObject("LDAP://".$this->server."/".$userDn, $this->adminUser, $this->adminPassword, 1);

        /*
         * Set the password
         */
        $user->SetPassword($password);
        
        return true;
        
    }
    
    private function getUser($username){
        return Corp::user($username);
    }
    
}