<?php

namespace ChannelEngine\ChannelEngineIntegration\Command;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\RepositoryClassException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * class ConfigChangeCommand
 * @package ChannelEngine\ChannelEngineIntegration\Command
 */
class ConfigChangeCommand extends Command
{

    /**
     * @var Init
     */
    private $initializer;

    public function __construct(Init $initializer)
    {
        $this->initializer = $initializer;
        parent::__construct();
    }

    protected function configure()
    {
        $options = [
            new InputOption(
               'key',
                null,
                InputOption::VALUE_REQUIRED,
                'key'
            ),

            new InputOption(
                'value',
                null,
                InputOption::VALUE_REQUIRED,
                'key'
            ),
            new InputOption(
                'store',
                null,
                InputOption::VALUE_OPTIONAL,
                'store'
            ),

        ];

        $this->setName('ce:set-config');
        $this->setDescription('Config Value command line');
        $this->setDefinition($options);
        parent::configure();
    }

    /**
     * @throws RepositoryClassException
     * @throws QueryFilterInvalidParamException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initializer->init();
        $key  = $input->getOption('key');
        $value  = $input->getOption('value');
        /** @var ConfigurationManager $configManager */
        $configManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);

        if ($context = $input->getOption('store')) {
            $configManager->setContext($context);
        }
        $configManager->saveConfigValue($key, $value, (bool)$context);

        return Cli::RETURN_SUCCESS;
    }
}
