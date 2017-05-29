<?php

/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 14/05/2017
 * Time: 10:58
 */

namespace AppBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class User extends BaseUser
{
    /**
     * @MongoDB\Field(type="string")
     */
    protected $firstName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $lastName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $gender;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $team;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $progress;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $photo;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $vk_id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $fb_id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="UserInvitation")
     * @Assert\NotNull()
     */
    protected $invitation;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param mixed $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return mixed
     */
    public function getFbId()
    {
        return $this->fb_id;
    }

    /**
     * @param mixed $fb_id
     */
    public function setFbId($fb_id)
    {
        $this->fb_id = $fb_id;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getVkId()
    {
        return $this->vk_id;
    }

    /**
     * @param mixed $vk_id
     */
    public function setVkId($vk_id)
    {
        $this->vk_id = $vk_id;
    }

    /**
     * @return UserInvitation
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * @param UserInvitation $invitation
     */
    public function setInvitation(UserInvitation $invitation)
    {
        $this->invitation = $invitation;
    }
}
