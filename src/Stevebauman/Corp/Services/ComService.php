<?php

namespace Stevebauman\Corp\Services;

class ComService
{
    /*
     * Holds the UserComService object
     */
    private $user;

    public function __construct()
    {

        /*
         * Construct a new UserComService object and inject the current Corp object
         */
        $this->user = new UserComService();
    }

    public function user()
    {
        return $this->user;
    }
}
