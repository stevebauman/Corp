<?php

namespace Stevebauman\Corp\Objects;

class User {
    
    public $username = '';
    
    public $name = '';
    
    public $email = '';
    
    public $group = '';
    
    public $type = '';
    
    public $dn = array();
    
    public function __construct(array $user)
    {
        $this->assign($user);
    }
    
    /**
     * Assigns object variables from adldap array
     * 
     * @param type $user
     */
    private function assign($user)
    {
        
        if(array_key_exists('dn', $user[0]))
        {
            $this->setDn(ldap_explode_dn($user[0]['dn'], 1));
        }
        
        if(array_key_exists('samaccountname', $user[0]))
        {
            $this->setUsername($user[0]['samaccountname'][0]);
        }
        
        if(array_key_exists('displayname', $user[0]))
        {
            $this->setName($user[0]['displayname'][0]);
        }
        
        if(array_key_exists('mail', $user[0]))
        {
            $this->setEmail($user[0]['mail'][0]);
        }
        
        if(array_key_exists(1, $this->dn))
        {
            $this->setType($this->dn[1]);
        }
        
        if(array_key_exists(2, $this->dn))
        {
            $this->setGroup($this->dn[2]);
        }
        
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setGroup($group)
    {
        $this->group = $group;
    }
    
    public function setType($type)
    {
        $this->type = $type;
    }
    
    public function setDn($dn)
    {
        $this->dn = $dn;
    }
    
}