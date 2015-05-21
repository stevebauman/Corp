<?php

namespace Stevebauman\Corp\Services;

use COM;
use Illuminate\Support\Facades\Config;
use Stevebauman\Corp\CorpServiceProvider;
use Stevebauman\Corp\Facades\Corp;

class UserComService
{
    /**
     * Holds the COM object.
     *
     * @var COM
     */
    private $com;

    /**
     * Holds the server name.
     *
     * @var string
     */
    private $server = '';

    /**
     * Holds the administrator username.
     *
     * @var string
     */
    private $adminUser = '';

    /**
     * Holds the administrator password.
     *
     * @var string
     */
    private $adminPassword = '';

    /**
     * Holds the COM's constructor LDAP parameter.
     *
     * @var string
     */
    private $ldapComCommand = 'LDAP:';

    /**
     * Constructor.
     */
    public function __construct()
    {
        /*
         * Construct a new COM object
         */
        if (class_exists('COM')) {
            $this->com = new COM($this->ldapComCommand);

            $this->server = Config::get('corp'.CorpServiceProvider::$configSeparator.'adldap_config.domain_controllers.0');
            $this->adminUser = Config::get('corp'.CorpServiceProvider::$configSeparator.'adldap_config.admin_username');
            $this->adminPassword = Config::get('corp'.CorpServiceProvider::$configSeparator.'adldap_config.admin_password');
        }
    }

    /**
     * Sets an LDAP user password using COM.
     *
     * @param string $password
     *
     * @return bool
     */
    public function password($username, $password)
    {
        /*
         * Get the user
         */
        $corpUser = $this->getUser($username);

        /*
         * Get the DS object
         */
        $user = $this->getDsObject($corpUser->dn_string);

        /*
         * Set the password
         */
        $user->SetPassword($password);

        /*
         * Save Object
         */
        $user->SetInfo();

        return true;
    }

    /**
     * Activates an LDAP account using COM.
     *
     * @param $username
     *
     * @return bool
     */
    public function activate($username)
    {
        /*
         * Get the user
         */
        $corpUser = $this->getUser($username);

        /*
         * Get the DS object
         */
        $user = $this->getDsObject($corpUser->dn_string);

        /*
         * Enable the account
         */
        $user->AccountDisabled = false;

        /*
         * Save Object
         */
        $user->SetInfo();

        return true;
    }

    /**
     * Returns a COM object using the specified user distinguished name.
     *
     * @param $userDn
     *
     * @return mixed
     */
    private function getDsObject($userDn)
    {
        return $this->com->OpenDSObject('LDAP://'.$this->server.'/'.$userDn, $this->adminUser, $this->adminPassword, 1);
    }

    /**
     * @param string $username
     *
     * @return mixed
     */
    private function getUser($username)
    {
        return Corp::user($username);
    }
}
