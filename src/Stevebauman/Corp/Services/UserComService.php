<?php

namespace Stevebauman\Corp\Services;

use COM;
use Illuminate\Support\Facades\Config;
use Stevebauman\Corp\Objects\User;
use Stevebauman\Corp\Services\ServiceInterface;

class UserComService implements ServiceInterface {
    
    private $user;
    
    private $com;
    
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->com = new COM('LDAP:');
    }
    
    /**
     * Sets an LDAP user password using COM
     * 
     * @param type $password
     * @return boolean
     */
    public function password($password)
    {
        
        /*
         * Grab the configuration values
         */
        $server         = Config::get('corp::adldap_config.domain_controllers.0');
        $adminUser      = Config::get('corp::adldap_config.admin_username');
        $adminPassword  = Config::get('corp::adldap_config.admin_password');
        
        /*
         * Grab the distiguished name from the user object
         */
        $userDn = $this->user->dn_string;
        
        /*
         * Get the DS object
         */
        $user = $this->com->OpenDSObject("LDAP://".$server."/".$userDn, $adminUser, $adminPassword, 1);
        
        /*
         * Set the password
         */
        $user->SetPassword($password);
        
        return true; 
    }
    
}