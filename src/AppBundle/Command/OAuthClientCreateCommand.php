<?php

namespace AppBundle\Command;

use AppBundle\Document\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OAuthClientCreateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('app:oauth_client:create')
            ->setDescription('Creates oAuth Client')
            ->addArgument('redirectUri', InputArgument::REQUIRED, 'Redirect URI')
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redirectUri = $input->getArgument('redirectUri');

        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        /** @var Client $client */
        $client = $clientManager->createClient();
        $client->setRedirectUris([$redirectUri]);
        $client->setAllowedGrantTypes(array('token', 'authorization_code', 'password'));

        $clientManager->updateClient($client);

        $output->writeln('<info>oAuth Client is created</info>');
        $output->writeln('Public ID: <info>' . $client->getPublicId() . '</info>');
        $output->writeln('Random ID: <info>' . $client->getRandomId() . '</info>');
        $output->writeln('Redirect URIs: <info>' . implode(', ', $client->getRedirectUris()) . '</info>');
    }
}
