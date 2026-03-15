<?php

namespace TrentRiddington\CustomerMetrics\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;

readonly class CustomerMetricsUpdater
{
    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private ResourceConnection $resourceConnection
    ) {
    }

    /**
     * Recalculate and update customer sales metrics.
     *
     * @param int[]|null $orderIds
     * @return void
     */
    public function execute(?array $orderIds = null): void
    {
        $updates = $this->calculateMetrics($orderIds);

        if (empty($updates)) {
            return;
        }

        $this->updateMetrics($updates);
    }

    /**
     * Update customer metrics for all or specified orders.
     *
     * This method calculates the lifetime revenue for each customer based on completed orders.
     * If an array of order IDs are passed, we get the customer IDs for those respective orders and calculate the
     * lifetime revenue for just those customers.
     *
     * @param int[]|null $orderIds
     * @return null|array
     */
    private function calculateMetrics(?array $orderIds = null): ?array
    {
        $connection = $this->resourceConnection->getConnection();
        $salesOrderTable = $this->resourceConnection->getTableName('sales_order');

        $customerIds = null;

        if ($orderIds) {
            // Get a list of customer IDs to recalculate the customer metrics based on the provided order IDs
            $customerIds = $connection->fetchCol(
                $connection->select()
                    ->distinct()
                    ->from($salesOrderTable, ['customer_id'])
                    ->where('entity_id IN (?)', $orderIds)
                    ->where('customer_id IS NOT NULL')
            );

            // End calculation if there are no associated customers
            if (empty($customerIds)) {
                return null;
            }
        }

        $select = $connection->select()
            ->from(
                $salesOrderTable,
                [
                    'customer_id',
                    'lifetime_revenue' => 'SUM(grand_total)'
                ]
            )
            ->where('status = ?', Order::STATE_COMPLETE)
            ->where('state = ?', Order::STATE_COMPLETE)
            ->group('customer_id');

        if ($customerIds) {
            $select->where('customer_id IN (?)', $customerIds);
        }

        return $connection->fetchAll($select);
    }

    /**
     * Insert or update customer metrics in the database.
     *
     * This method batches the customer metrics data to avoid large single queries.
     *
     * @param array $updates
     * @return void
     */
    private function updateMetrics(array $updates): void
    {
        $connection = $this->resourceConnection->getConnection();
        $customerMetricsTable = $this->resourceConnection->getTableName('customer_metrics');

        $batchSize = 1000;

        foreach (array_chunk($updates, $batchSize) as $batchUpdates) {
            $connection->insertOnDuplicate(
                $customerMetricsTable,
                $batchUpdates,
                ['customer_id', 'lifetime_revenue']
            );
        }
    }
}
