<?php

namespace Stevebauman\Corp\Objects;

class ComputerOs {
    
    public $name = '';
    
    public $version = '';
    
    public $service_pack = '';
    
    public function __construct($osInfo){
        $this->assign($osInfo);
    }
    
    private function assign($os){
        
        if(array_key_exists('operatingsystem', $os))
        {
           $this->name = $os['operatingsystem'][0]; 
        }
   
        if(array_key_exists('operatingsystemservicepack', $os))
        {
            $this->service_pack = $os['operatingsystemservicepack'][0];
        }
        
        if(array_key_exists('operatingsystemversion', $os))
        {
            $this->version = $os['operatingsystemversion'][0];
        }
        
    }
    
}