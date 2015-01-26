<?php

namespace Stevebauman\Corp\Objects;

class Computer {
    
    public $name = '';
    
    public $os;
    
    public $type = '';
    
    public $group = '';
    
    public $dn = array();
    
    public $host_name = '';
    
    public function __construct(array $computerInfo)
    {
        $this->assign($computerInfo);
    }
    
    private function assign($computer)
    {
        
        if(array_key_exists(0, $computer) && $computer['count'] > 0){
            
            if(array_key_exists('dn', $computer[0])){
                $this->dn = ldap_explode_dn($computer[0]['dn'], 1);
                
                $this->name = $this->dn[0];

                if(array_key_exists(1, $this->dn)){
                    $this->group = $this->dn[1];
                }

                if(array_key_exists(2, $this->dn)){
                    $this->type = $this->dn[2];
                }

                if(array_key_exists('dnshostname', $computer[0])){
                    $this->host_name = $computer[0]['dnshostname'][0];
                }

                $this->os = new ComputerOs($computer[0]);  
            }
        }
    }
    
    
}