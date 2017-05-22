<?php

/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 14/05/2017
 * Time: 10:58
 */

namespace AppBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class User extends BaseUser
{

    protected $firstName;

    protected $lastName;

    protected $gender;

    protected $team;

    protected $progress;

    protected $photo;

    protected $vk_id;

    protected $fb_id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

}