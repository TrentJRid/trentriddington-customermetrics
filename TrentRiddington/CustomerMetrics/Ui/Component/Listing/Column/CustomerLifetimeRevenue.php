<?php

namespace TrentRiddington\CustomerMetrics\Ui\Component\Listing\Column;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CustomerLifetimeRevenue extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface                        $context,
        UiComponentFactory                      $uiComponentFactory,
        private readonly PriceCurrencyInterface $priceFormatter,
        array                                   $components = [],
        array                                   $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Format the price value for the customer lifetime revenue column in the sales_order_grid UI
     *
     * @param array $dataSource
     * @return array<string, mixed>
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['customer_lifetime_revenue'])) {
                continue;
            }

            $item['customer_lifetime_revenue'] = $this->priceFormatter->format(
                $item['customer_lifetime_revenue'],
                false
            );
        }

        return $dataSource;
    }
}
