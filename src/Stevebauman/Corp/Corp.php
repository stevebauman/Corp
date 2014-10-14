<?php 

namespace Stevebauman\Corp;

use adLDAP\adLDAP;
use Stevebauman\Corp\Objects\User;
use Stevebauman\Corp\Objects\Computer;
use Stevebauman\Corp\Objects\Printer;
use Illuminate\Support\Collection;
use Illuminate\Config\Repository;

class Corp {
	
        /*
         * Holds current ADLdap object
         */
	protected $adldap;
        
        /*
         * Holds laravels config
         */
	private $config;
	
	public function __construct(Repository $config){
            
            /*
             * Create Config object
             */
            $this->config = $config;
            
            /*
             * Create AdLDAP object
             */
            $this->adldap = new adLDAP($this->config->get('corp::adldap_config'));
	}
	
	/**
	 * Authenticates a user through adLDAP for accessing logged in functions. 
	 * Returns true if login is correct.
	 *
	 * @param  string $username, $password
	 * @return boolean
	 */
	public function auth($username, $password){
		if($this->adldap->user()->authenticate($username, $password)){
                    return true;
                } return false;
	}
        
        /**
         * Returns a user object from an aldap array with the specified username
         * 
         * @param type $username
         * @return \Stevebauman\Corp\Objects\User
         */
        public function user($username){
            return new User($this->adldap->user()->info($username));
        }
        
        /**
         * Returns a collection of user objects
         * 
         * @return \Illuminate\Support\Collection
         */
        public function users(){
            $adldapUsers = $this->adldap->user()->all();
            
            $users = array();
            
            // Excluded user types
            $excluded_types = $this->config->get('corp::options.users.excluded_user_types');

            // Excluded user groups
            $excluded_groups = $this->config->get('corp::options.users.excluded_user_groups');
            
            foreach($adldapUsers as $username){
                
                $user = new User($this->adldap->user()->info($username));
                
                if(!in_array($user->type, $excluded_types) && !in_array($user->group, $excluded_groups)){
                    $users[] = $user; 
                }
                
            }
            
            return new Collection($users);
        }
	

	/**
	 * Returns a computer object from an adldap array
	 *
	 * @param  string  $name
	 * @return \Stevebauman\Corp\Objects\Computer
	 */
	public function computer($name){
            $computer = new Computer($this->adldap->computer()->info($name));
            
            return $computer;
	}
        
        /**
         * Returns a collection of computer objects
         * 
         * @return \Illuminate\Support\Collection
         */
        public function computers(){
            $folders = $this->folder($this->config->get('corp::options.computers.folder'));
            
            $computers = array();
            
            foreach($folders as $computer)
            {
                if(is_array($computer))
                {
                    
                    if(array_key_exists('objectclass', $computer))
                    {
                        /*
                         * The object class array inside the computer array will
                         * contain the key '4' if it is a computer
                         */
                        if(array_key_exists(4, $computer['objectclass']))
                        {
                            $dn = ldap_explode_dn($computer['distinguishedname'][0], 2);
                            
                            $computers[] = $this->computer($dn[0]);
                            
                        }
                        
                    }
                    
                }
            }
            
            return new Collection($computers);
        }
        
        /**
         * Searches through an array of printers, if the name specified
         * equals the name of the printer, it is returned
         * 
         * @param type $name
         * @return \Stevebauman\Corp\Objects\Printer
         */
        public function printer($name){
            $printers = $this->printers();
            
            foreach($printers as $printer)
            {
                if($printer->name === $name)
                {
                    return $printer;
                }
            }
            
            return false;
        }
        
        /**
         * Returns a collection of printer objects
         * 
         * @return \Illuminate\Support\Collection
         */
        public function printers(){
            $folders = $this->folder($this->config->get('corp::options.computers.folder'));
            
            $printers = array();
            
            foreach($folders as $printer)
            {
                if(is_array($printer))
                {
                    
                    if(array_key_exists('objectclass', $printer))
                    {
                        /*
                         * The object class array inside the computer array will
                         * contain the key '4' if it is a computer
                         */
                        if(array_key_exists(3, $printer['objectclass']))
                        {
                            if($printer['objectclass'][3] === 'printQueue'){
                                $printers[] = new Printer($printer);
                            }
                        }
                        
                    }
                }
            }
            
            return new Collection($printers);
            
        }
        
        /**
         * Returns an AdLDAP folder listing
         * 
         * @param type $folder
         * @return type
         */
        public function folder($folder){
            return $this->adldap->folder()->listing($folder, adLDAP::ADLDAP_FOLDER, true);
        }
        
        /**
         * Returns current adLDAP object
         * 
         * @return adLDAP/adLDAP
         */
        public function adldap(){
            return $this->adldap;
        }
}

