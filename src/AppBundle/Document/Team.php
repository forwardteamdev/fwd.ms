<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 22/05/2017
 * Time: 12:27
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Team
{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    protected $title;

    protected $progress;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }
}
