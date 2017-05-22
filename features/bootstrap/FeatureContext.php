<?php

/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 15/05/2017
 * Time: 15:36
 */

use \Behat\Behat\Context\Context;
use \Symfony\Component\Process\Process;
use \Doctrine\Common\Persistence\ManagerRegistry;

class FeatureContext implements Context
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @BeforeScenario
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function createSchema()
    {
        $rootDir = $this->container->getParameter('kernel.root_dir');
        $env = '--env=' . $this->container->get('kernel')->getEnvironment();
        $consoleCommand = $rootDir . '/../bin/console';
        $dropCommand = $consoleCommand . ' doctrine:mongodb:schema:drop ' . $env;
        $createCommand = $consoleCommand . ' doctrine:mongodb:schema:create ' . $env;

        $this->runProcess($dropCommand);
        $this->runProcess($createCommand);
    }

    private function runProcess($command)
    {
        $processDrop = new Process($command);
        $processDrop->run();
        if (!$processDrop->isSuccessful()) {
            throw new \Symfony\Component\Process\Exception\ProcessFailedException($processDrop);
        }
    }
}
