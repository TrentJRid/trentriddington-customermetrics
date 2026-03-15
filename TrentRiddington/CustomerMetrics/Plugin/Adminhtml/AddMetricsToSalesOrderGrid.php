<?php

namespace TrentRiddington\CustomerMetrics\Plugin\Adminhtml;

use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;

class AddMetricsToSalesOrderGrid
{
    /**
     * Add customer metrics to the sales order grid collection.
     *
     * This plugin intercepts the admin order grid data collection and joins the customer_metrics table to surface
     * the customer lifetime revenue in the grid UI.
     *
     * @param CollectionFactory $subject
     * @param Collection $collection
     * @param string $requestName
     * @return Collection
     */
    public function afterGetReport(CollectionFactory $subject, Collection $collection, string $requestName): Collection
    {
        if ($requestName !== 'sales_order_grid_data_source' || !$collection instanceof OrderGridCollection) {
            return $collection;
        }

        if (!$collection->getFlag('customer_metrics_added')) {
            $collection->getSelect()
                ->joinLeft(
                    ['cm' => $collection->getTable('customer_metrics')],
                    'cm.customer_id = main_table.customer_id',
                    [
                        'customer_lifetime_revenue' => 'cm.lifetime_revenue'
                    ]
                );

            // Map customer_lifetime_revenue alias to real DB column so the grid UI filtering works
            $collection->addFilterToMap('customer_lifetime_revenue', 'cm.lifetime_revenue');

            $collection->setFlag('customer_metrics_added', true);
        }

        return $collection;
    }
}
