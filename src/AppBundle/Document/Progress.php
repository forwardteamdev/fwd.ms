<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 22/05/2017
 * Time: 12:27
 */

namespace AppBundle\Document;


class Progress
{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    protected $amount;

    protected $sprints;

    protected $challenges;

    protected $pins;

    protected $questions;

    protected $news;

    protected $votes;

}
