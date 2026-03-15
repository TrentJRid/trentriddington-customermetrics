<?php

namespace TrentRiddington\CustomerMetrics\Model\Indexer\SalesData\Action;

use TrentRiddington\CustomerMetrics\Model\CustomerMetricsUpdater;

readonly class Rows
{
    /**
     * @param CustomerMetricsUpdater $customerMetricsUpdater
     */
    public function __construct(
        private CustomerMetricsUpdater $customerMetricsUpdater
    ) {
    }

    /**
     * Perform a partial reindex of customer sales data metrics for specified order IDS.
     *
     * @param int[] $ids
     * @return void
     */
    public function execute(array $ids): void
    {
        $this->customerMetricsUpdater->execute($ids);
    }
}
