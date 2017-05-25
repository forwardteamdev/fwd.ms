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
class SprintRequirement
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    protected $title;

    protected $count;
}
