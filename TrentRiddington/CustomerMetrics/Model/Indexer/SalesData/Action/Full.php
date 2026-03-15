<?php

namespace TrentRiddington\CustomerMetrics\Model\Indexer\SalesData\Action;

use TrentRiddington\CustomerMetrics\Model\CustomerMetricsUpdater;

readonly class Full
{
    /**
     * @param CustomerMetricsUpdater $customerMetricsUpdater
     */
    public function __construct(
        private CustomerMetricsUpdater $customerMetricsUpdater
    ) {
    }

    /**
     * Perform a full reindex of all customer sales data metrics.
     *
     * @return void
     */
    public function execute(): void
    {
        $this->customerMetricsUpdater->execute();
    }
}
