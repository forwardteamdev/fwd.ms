<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 22/05/2017
 * Time: 12:27
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class UserInvitation
{
    const CODE_LENGTH = 8;

    /**
     * @MongoDB\Id(type="string", strategy="NONE")
     */
    protected $code;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\Email()
     */
    protected $email;

    /**
     * @MongoDB\Field(type="int")
     * @Assert\NotNull()
     */
    protected $team;


    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $sent;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->sent = false;
        $this->createdAt = new \DateTime();
        $this->code = substr(md5(uniqid(mt_rand(), true)), 0, self::CODE_LENGTH);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
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
     * @return $this
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param boolean $sent
     * @return $this
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }
}
