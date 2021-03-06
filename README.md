# magento2-module

**System requirement to support this module:**

Magento2 - 2.4.x with PHP7.4

# How to use the module
You need to clone it into the app/code/ folder for your Magento project, when its clone inside you need to check and enable it.

```
php bin/magento module:status
php bin/magento module:enable SmartPack_WMS
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

When you have enabled the module you need to config settings between SmartPack WMS API to your Magento2 webshop.

```
Stores > Configuration > SmartPack > WMS API Configuration
```

# Sync your products
First you need to sync your products to SmartPack WMS. Default its go for 50 product each sync loop. Its only sync products there are enabled.

```
bin/magento smartpack:product:sync
```

_Supported product types: Simple Product_

# Sync sales orders
Sync sales order if its marked ready for shipment.

```
bin/magento smartpack:order:sync
```


## Module Featuers
SmartPack have many functions, this module will support following integrations between Magento2 and SmartPack systems

**WMS**
- Product
    - CLI: Full sync of products from shop to WMS integration
    - Cron: sync product changes from shop to WMS integration
- Order
    - CLI: Sync all with shipment status
    - Hook: complate order on shipment hook
- Stock
    - Hook: update product stock on hook signals
