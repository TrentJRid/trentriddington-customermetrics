# Customer Metrics module

This module serves to calculate and display customer metrics data and adds a new Customer Lifetime Revenue column to the Magento Admin Sales Order Grid.

The module has been designed to be extended so additional customer metrics can be calculated and displayed in the future, see the [Extending the metrics](#extending-the-metrics) section.

## Data strategy

The `customer_metrics` table has been created to store the customer lifetime revenue information.

Customer metrics are updated through a new `customermetrics_salesdata` indexer which leverages the mview process.

This indexer listens for changes in the `sales_order` table and then recalculates the metrics for only the affected customers (e.g. when the indexer is run via `bin/magento cron:run`), ensuring only the necessary metrics are updated without requiring a full reindex. 

When orders change and the cron is run, the indexer:

1. Identifies the customers associated with the changed orders
2. Recalculates metrics using all completed orders for those customers
3. Performs batched database query updates to update the metrics

Customer metrics can be calculated for all existing orders by running a full reindex.

## Data flow

The data flow for mview updates is as follows:

1. `sales_order` updates
2. mview indexer triggers
3. affected customers are identified
4. customer sales data metrics recalculated
5. `customer_metrics` table updated
6. admin order grid joins `customer_metrics` table and surfaces results

This approach avoids non-performant runtime queries during the admin grid rendering.  

## Scalability considerations

The following design choices have been implemented to ensure the solution is performant:

### Pre-aggregated data

The sales order grid fetches metrics from a materialised table instead of calculating the values on load, preventing slow `SUM()` operations on the `sales_order` table.

### Adhoc indexing updates

The indexer only recalculates data for customers whose orders have changed. This reduces indexing overhead when Magento's cron is run.

### Batching database writes

Updates to the `customer_metrics` table are applied using batched insertOnDuplicate queries, enabling many records to be updated in a single query and improving efficiency.

### Efficient order grid customer metrics

The admin grid collection includes a simple join on the `customer_metrics` table to include the lifetime revenue information.

## Extending the metrics

The design of this module separates the customer metrics from core Magento entities/tables by storing them in a dedicated table.

This approach allows additional metrics to easily be added from this module.

Future metrics could include:
* Average Order Value
* Total Orders
* Last Order Date

These metrics can be added as additional columns to the `customer_metrics` table and then calculated during the `customermetrics_salesdata` indexing process.

This approach enables the `TrentRiddington_CustomerMetrics` module to evolve into a multi-purpose customer metrics provider.

