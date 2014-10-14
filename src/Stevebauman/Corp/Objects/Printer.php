<?php

namespace Stevebauman\Corp\Objects;

use Illuminate\Support\Facades\Config;

class Printer {
    
    public $name = '';
    
    public $dn = array();
    
    public function __construct($printerInfo) {
        $this->assign($printerInfo);
    }
    
    private function assign($printer)
    {
        $this->dn = ldap_explode_dn($printer['dn'], 1);
        
        $this->name = $this->dn[0];
    }
    
}
