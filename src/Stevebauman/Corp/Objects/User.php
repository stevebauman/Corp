<?php

namespace Stevebauman\Corp\Objects;

use Stevebauman\Corp\Services\UserComService;

class User {
    
    public $username = '';
    
    public $name = '';
    
    public $email = '';
    
    public $group = '';
    
    public $type = '';
    
    public $dn = array();
    
    public $dn_string = '';
    
    /*
     * Holds the service to change user information
     */
    private $service;
    
    public function __construct(array $user)
    {
        $this->assign($user);
        
        $this->service = new UserComService;
    }
    
    /**
     * Assigns object variables from adldap array
     *
     * @param $user
     */
    private function assign($user)
    {
        
        if(array_key_exists('dn', $user[0]))
        {
            $this->dn = ldap_explode_dn($user[0]['dn'], 1);
            $this->dn_string = $user[0]['dn'];
        }
        
        if(array_key_exists('samaccountname', $user[0]))
        {
            $this->username = $user[0]['samaccountname'][0];
        }
        
        if(array_key_exists('displayname', $user[0]))
        {
            $this->name = $user[0]['displayname'][0];
        }
        
        if(array_key_exists('mail', $user[0]))
        {
            $this->email = $user[0]['mail'][0];
        }
        
        if(array_key_exists(1, $this->dn))
        {
            $this->type = $this->dn[1];
        }
        
        if(array_key_exists(2, $this->dn))
        {
            $this->group = $this->dn[2];
        }
        
    }
    
}