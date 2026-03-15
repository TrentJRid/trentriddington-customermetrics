<?php

namespace TrentRiddington\CustomerMetrics\Model\Indexer;

use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class SalesData implements ActionInterface, MviewActionInterface
{
    /**
     * @param SalesData\Action\Full $salesDataIndexerFull
     * @param SalesData\Action\Rows $salesDataIndexerRows
     */
    public function __construct(
        private readonly SalesData\Action\Full $salesDataIndexerFull,
        private readonly SalesData\Action\Rows $salesDataIndexerRows
    ) {
    }

    /**
     * Full reindex of the customer sales data metrics
     *
     * This method triggers a full reindex for the customer sales data metrics.
     *
     * @inheritDoc
     * @return void
     */
    public function executeFull(): void
    {
        $this->salesDataIndexerFull->execute();
    }

    /**
     * Partial index of the customer sales data metrics by order ID list
     *
     * This method triggers a reindex for the provided list of order IDs.
     *
     * @inheritDoc
     * @return void
     */
    public function execute($ids): void
    {
        $this->salesDataIndexerRows->execute($ids);
    }

    /**
     * Partial index of the customer sales data metrics by order ID list
     *
     * This method triggers a reindex for the provided list of order IDs.
     * It is triggered automatically when the Magento cron runs.
     *
     * @inheritDoc
     * @return void
     */
    public function executeList(array $ids): void
    {
        $this->salesDataIndexerRows->execute($ids);
    }

    /**
     * Partial index of a customer sales data metric by order ID
     *
     * This method triggers a reindex for one order ID.
     *
     * @inheritDoc
     * @return void
     */
    public function executeRow($id): void
    {
        $this->salesDataIndexerRows->execute([$id]);
    }
}
