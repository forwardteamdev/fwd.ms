<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 22/05/2017
 * Time: 12:27
 */

namespace AppBundle\Document;


class Sprint
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    protected $title;

    protected $subtitle;

    protected $requirements;
}
