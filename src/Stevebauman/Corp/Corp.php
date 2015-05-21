<?php

namespace Stevebauman\Corp;

use adLDAP\adLDAP;
use Stevebauman\Corp\Objects\User;
use Stevebauman\Corp\Objects\Computer;
use Stevebauman\Corp\Objects\Printer;
use Stevebauman\Corp\Services\ComService;
use Illuminate\Support\Collection;
use Illuminate\Config\Repository;

/**
 * Class Corp.
 */
class Corp
{
    /*
     * Holds current ADLdap object
     */
    protected $adldap;

    /*
     * Holds laravels config
     */
    private $config;

    /*
     * Holds COM object if COM is enabled
     */
    private $com;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {

        /*
         * Create Config object
         */
        $this->config = $config;

        /*
         * Create AdLDAP object
         */
        $this->adldap = new adLDAP($this->getAdldapConfig());

        /*
         * Create ComService object
         */
        $this->com = new ComService();
    }

    /**
     * Returns current adLDAP object.
     *
     * @return adLDAP
     */
    public function adldap()
    {
        return $this->adldap;
    }

    /**
     * Returns the current COM service instance.
     *
     * @return ComService
     */
    public function com()
    {
        return $this->com;
    }

    /**
     * Authenticates a user through adLDAP for accessing logged in functions.
     * Returns true if login is correct.
     *
     * @param string $username , $password
     *
     * @return bool
     */
    public function auth($username, $password)
    {
        return $this->adldap()->user()->authenticate($username, $password);
    }

    /**
     * Returns a user object from an aldap array with the specified username.
     *
     * @param $username
     *
     * @return bool|User
     */
    public function user($username)
    {
        $user = $this->adldap()->user()->info($username);

        if ($user) {
            return new User($user);
        } else {
            return false;
        }
    }

    /**
     * Returns a filtered collection of user objects.
     *
     * @return Collection
     */
    public function users()
    {
        $adldapUsers = $this->getAllUsers();

        $users = [];

        foreach ($adldapUsers as $username) {
            $user = new User($this->adldap->user()->info($username));

            $users[] = $user;
        }

        return new Collection(array_filter($users, [$this, 'filterUser']));
    }

    /**
     * Returns a computer object from an adldap array with the specified.
     *
     * @param $name
     *
     * @return Computer
     */
    public function computer($name)
    {
        $computer = new Computer($this->adldap()->computer()->info($name));

        return $computer;
    }

    /**
     * Returns a collection of computer objects.
     *
     * @return Collection
     */
    public function computers()
    {
        $folders = $this->folder($this->getComputersFolder());

        $computers = [];

        foreach ($folders as $computer) {
            if (is_array($computer)) {
                if (array_key_exists('objectclass', $computer)) {
                    /*
                     * The object class array inside the computer array will
                     * contain the key '4' if it is a computer
                     */
                    if (array_key_exists(4, $computer['objectclass'])) {
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
     * equals the name of the printer, it is returned.
     *
     * @param $name
     *
     * @return bool|mixed
     */
    public function printer($name)
    {
        $printers = $this->printers();

        foreach ($printers as $printer) {
            if ($printer->name === $name) {
                return $printer;
            }
        }

        return false;
    }

    /**
     * Returns a collection of printer objects.
     *
     * @return Collection
     */
    public function printers()
    {
        $folders = $this->folder($this->getComputersFolder());

        $printers = [];

        foreach ($folders as $printer) {
            if (is_array($printer)) {
                if (array_key_exists('objectclass', $printer)) {
                    /*
                     * The object class array inside the computer array will
                     * contain the key '3' if it is a printer
                     */
                    if (array_key_exists(3, $printer['objectclass'])) {
                        if ($printer['objectclass'][3] === 'printQueue') {
                            $printers[] = new Printer($printer);
                        }
                    }
                }
            }
        }

        return new Collection($printers);
    }

    /**
     * Returns an AdLDAP folder listing.
     *
     * @param $folder
     *
     * @return array
     */
    public function folder($folder)
    {
        return $this->adldap()->folder()->listing($folder, adLDAP::ADLDAP_FOLDER, true);
    }

    /**
     * Filters the specified user against the excluded user types and groups.
     *
     * @param User $user
     *
     * @return bool|User
     */
    private function filterUser(User $user)
    {
        if (!in_array($user->type, $this->getExcludedUserTypes()) || !in_array($user->group, $this->getExcludedUserGroups())) {
            return $user;
        }

        return false;
    }

    /**
     * Returns all users from adldap.
     *
     * @return array
     */
    private function getAllUsers()
    {
        return $this->adldap()->user()->all();
    }

    /**
     * Returns adldap configuration from the config file.
     *
     * @return mixed
     */
    private function getAdldapConfig()
    {
        return $this->config->get('corp::adldap_config');
    }

    /**
     * Returns the excluded user groups in the config file.
     *
     * @return array
     */
    private function getExcludedUserGroups()
    {
        $groups = $this->config->get('corp::options.users.excluded_user_groups');

        return (is_array($groups) ? $groups : []);
    }

    /**
     * Returns the excluded user types in the config file.
     *
     * @return array
     */
    private function getExcludedUserTypes()
    {
        $types = $this->config->get('corp::options.users.excluded_user_types');

        return (is_array($types) ? $types : []);
    }

    /**
     * Returns the computers folder in the config file.
     *
     * @return mixed
     */
    private function getComputersFolder()
    {
        return $this->config->get('corp::options.computers.folder');
    }
}
