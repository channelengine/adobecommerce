<?php

namespace ChannelEngine\ChannelEngineIntegration\Setup\Patch\Schema;

use _PHPStan_3bfe2e67c\Nette\Neon\Exception;
use ChannelEngine\ChannelEngineIntegration\Setup\Patch\AbstractPatch;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class AddTypeOfFulfillmentColumnInCeOrderTable extends AbstractPatch implements SchemaPatchInterface
{
    /**
     * @return array|string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $connection->addColumn(
            $this->moduleDataSetup->getTable('channel_engine_order'),
            'channel_type_of_fulfillment',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'ChannelEngine channel type of fulfillment'
            ]
        );

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return string
     */
    public static function getVersion(): string
    {
        return '1.0.2';
    }
}
