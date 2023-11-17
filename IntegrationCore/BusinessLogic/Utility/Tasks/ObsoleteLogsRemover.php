<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\Utility\Tasks;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\TaskExecution\Task;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;
use DateTime;

class ObsoleteLogsRemover extends Task
{
    const LOG_KEEP_DAYS = 30;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $cutoff = $this->getTimeProvider()->getCurrentLocalTime();
        $cutoff = $cutoff->modify("- " . static::LOG_KEEP_DAYS . " days");

        $this->reportProgress(5);

        $this->deleteLogs($cutoff);

        $this->reportProgress(70);

        $this->deleteDetails($cutoff);

        $this->reportProgress(100);
    }

    private function getLogRepository()
    {
        return RepositoryRegistry::getRepository(TransactionLog::getClassName());
    }

    private function getDetailsRepository()
    {
        return RepositoryRegistry::getRepository(Details::getClassName());
    }

    /**
     * @return TimeProvider
     */
    private function getTimeProvider()
    {
        return ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }

    private function deleteLogs(DateTime $cutoff)
    {
        $filter = new QueryFilter();
        $filter->where('startTime', Operators::LESS_THAN, $cutoff);

        $this->getLogRepository()->deleteWhere($filter);
    }

    private function deleteDetails(DateTime $cutoff)
    {
        $filter = new QueryFilter();
        $filter->where('createdAt', Operators::LESS_THAN, $cutoff);

        $this->getDetailsRepository()->deleteWhere($filter);
    }
}