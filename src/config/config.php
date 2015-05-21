<?php

/*
 * The Corp configuration file
 *
 * @package Stevebauman/Corp
 */
return [

    /*
     * AdLDAP Configuration
     */
    'adldap_config' => [

        /*
         * The account suffix of the domain
         */
        'account_suffix' => '@domain.org',

        /*
         * An array of domains for load balancing
         *
         * Must be array
         */
        'domain_controllers' => ['domain.org', 'domain2.org'],

        /*
         * The base distinguished name
         *
         * ex. DC=domain,DC=local
         */
        'base_dn' => '',

        /*
         * The administrator username
         */
        'admin_username' => 'admin',

        /*
         * The administrator password
         */
        'admin_password' => '12345',

        /*
         * Returns the primary group (an educated guess)
         */
        'real_primary_group' => true,

        /*
         * Both can be false if no SSL/TLS is used on server
         *
         * Must be boolean
         */
        'use_ssl' => false, // If TLS is true this MUST be false.
        'use_tls' => false, // If SSL is true this MUST be false.

        'recursive_groups' => true,

    ],

    /*
     * Corp Configuration
     *
     * You can edit some options here for some customization of results on functions
     */
    'options' => [

        /*
         * The Corp::users() configuration
         */
        'users' => [

            /*
             * When using function Corp::users(), certain group types can be excluded so service accounts and
             * other non-human accounts so they don't cloud your results
             *
             * Must be array
             */
            'excluded_user_types' => [
                'Service Accounts',
                'Microsoft Exchange System Objects',
            ],

            /*
             * You can also exclude specific groups from your user results
             *
             * Must be array
             */
            'excluded_user_groups' => [
                'corp',
            ],

        ],

        /*
         * The Corp::computers() configuration
         */
        'computers' => [

            /*
             * The folder to search for computers in.
             *
             * Must be array
             */
            'folder' => ['Computers'],
        ],
    ],

];
