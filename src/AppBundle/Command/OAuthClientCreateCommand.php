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
            ->addArgument('redirectUri', InputArgument::REQUIRED, 'Redirect URI, use comma to separate several URIs')
        ;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redirectUri = $input->getArgument('redirectUri');
        $uri = explode(',', $redirectUri);

        $clientService = $this->getContainer()->get('app.oauth.client');
        $client = $clientService->create($uri);

        $output->writeln('<info>oAuth Client is created</info>');
        $output->writeln('Public ID: <info>' . $client->getPublicId() . '</info>');
        $output->writeln('Random ID: <info>' . $client->getSecret() . '</info>');
        $output->writeln('Redirect URIs: <info>' . implode(', ', $client->getRedirectUris()) . '</info>');
    }
}
