<?php

namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\Details;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\TransactionLog\Entities\TransactionLog;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\Operators;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\QueryFilter\QueryFilter;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\RepositoryRegistry;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Utility\TimeProvider;

class DetailsService implements Contracts\DetailsService
{
    /**
     * @inheritDoc
     */
    public function create(TransactionLog $log, $message, $arguments = [])
    {
        $details = new Details();
        $details->setLogId($log->getId());
        $details->setMessage($message);
        $details->setArguments($arguments);
        $details->setCreatedAt($this->getTimeProvider()->getCurrentLocalTime());
        $details->setContext($log->getContext());

        $this->getRepository()->save($details);
    }

    /**
     * @inheritDoc
     */
    public function getForLog($logId)
    {
        $filter = new QueryFilter();
        $filter->where('logId', Operators::EQUALS, $logId);

        return $this->getRepository()->select($filter);
    }

    /**
     * @inheritDoc
     */
    public function find(array $query = [], $offset = 0, $limit = 1000)
    {
        $queryFilter = new QueryFilter();
        $queryFilter->setLimit($limit);
        $queryFilter->setOffset($offset);

        foreach ($query as $column => $value) {
            if ($value === null) {
                $queryFilter->where($column, Operators::NULL);
            } else {
                $queryFilter->where($column, Operators::EQUALS, $value);
            }
        }

        return $this->getRepository()->select($queryFilter);
    }

    /**
     * Retrieves number of transaction logs.
     *
     * @return int
     *
     * @throws QueryFilterInvalidParamException
     */
    public function count(array $query = [])
    {
        $queryFilter = new QueryFilter();

        foreach ($query as $column => $value) {
            if ($value === null) {
                $queryFilter->where($column, Operators::NULL);
            } else {
                $queryFilter->where($column, Operators::EQUALS, $value);
            }
        }

        return $this->getRepository()->count($queryFilter);
    }

    private function getRepository()
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
}