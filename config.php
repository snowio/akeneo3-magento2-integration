<?php

use function DI\get;
use SnowIO\Akeneo3Magento2\Integrations\AkeneoMagentoConfiguration;
use SnowIO\Akeneo3Magento2\Integrations\DefaultAkeneoMagentoConfiguration;
use SnowIO\Akeneo3Magento2\Integrations\DefaultProductModelTransform;
use SnowIO\Akeneo3Magento2\Integrations\DefaultProductTransform;
use SnowIO\Akeneo3Magento2\Integrations\Functions\DeleteAttribute;
use SnowIO\Akeneo3Magento2\Integrations\Functions\DeleteAttributeOption;
use SnowIO\Akeneo3Magento2\Integrations\Functions\DeleteCategory;
use SnowIO\Akeneo3Magento2\Integrations\Functions\DeleteConfigurableProducts;
use SnowIO\Akeneo3Magento2\Integrations\Functions\DeleteSimpleProduct;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultDeleteAttribute;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultDeleteAttributeOption;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultDeleteCategory;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultDeleteConfigurableProducts;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultDeleteSimpleProduct;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultMoveCategory;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveAttribute;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveAttributeOption;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveCategory;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveConfigurableProductCategoryAssociations;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveConfigurableProducts;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveSimpleProductCategoryAssociations;
use SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation\DefaultSaveSimpleProducts;
use SnowIO\Akeneo3Magento2\Integrations\Functions\MoveCategory;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveAttribute;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveAttributeOption;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveCategory;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveConfigurableProductCategoryAssociations;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveConfigurableProducts;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveSimpleProductCategoryAssociations;
use SnowIO\Akeneo3Magento2\Integrations\Functions\SaveSimpleProducts;
use SnowIO\Akeneo3Magento2\Integrations\ProductModelTransform;
use SnowIO\Akeneo3Magento2\Integrations\ProductTransform;

return [
    //Mappings Functions
    'Akeneo3Magento2::SaveCategory' => get(SaveCategory::class),
    'Akeneo3Magento2::DeleteAttribute' => get(DeleteAttribute::class),
    'Akeneo3Magento2::DeleteAttributeOption' => get(DeleteAttributeOption::class),
    'Akeneo3Magento2::DeleteCategory' => get(DeleteCategory::class),
    'Akeneo3Magento2::DeleteConfigurableProducts' => get(DeleteConfigurableProducts::class),
    'Akeneo3Magento2::DeleteSimpleProduct' => get(DeleteSimpleProduct::class),
    'Akeneo3Magento2::MoveCategory' => get(MoveCategory::class),
    'Akeneo3Magento2::SaveAttribute' => get(SaveAttribute::class),
    'Akeneo3Magento2::SaveAttributeOption' => get(SaveAttributeOption::class),
    'Akeneo3Magento2::SaveConfigurableProductCategoryAssociations' => get(SaveConfigurableProductCategoryAssociations::class),
    'Akeneo3Magento2::SaveConfigurableProducts' => get(SaveConfigurableProducts::class),
    'Akeneo3Magento2::SaveSimpleProductCategoryAssociations' => get(SaveSimpleProductCategoryAssociations::class),
    'Akeneo3Magento2::SaveSimpleProducts' => get(DefaultSaveSimpleProducts::class),

    AkeneoMagentoConfiguration::class => DefaultAkeneoMagentoConfiguration::class,
    ProductTransform::class => DefaultProductTransform::class,
    ProductModelTransform::class => DefaultProductModelTransform::class,
    //Default Implementations Of The Mapping Function Interfaces
    SaveCategory::class => get(DefaultSaveCategory::class),
    DeleteAttribute::class => get(DefaultDeleteAttribute::class),
    DeleteAttributeOption::class => get(DefaultDeleteAttributeOption::class),
    DeleteCategory::class => get(DefaultDeleteCategory::class),
    DeleteConfigurableProducts::class => get(DefaultDeleteConfigurableProducts::class),
    DeleteSimpleProduct::class => get(DefaultDeleteSimpleProduct::class),
    MoveCategory::class => get(DefaultMoveCategory::class),
    SaveAttribute::class => get(DefaultSaveAttribute::class),
    SaveAttributeOption::class => get(DefaultSaveAttributeOption::class),
    SaveConfigurableProductCategoryAssociations::class => get(DefaultSaveConfigurableProductCategoryAssociations::class),
    SaveConfigurableProducts::class => get(DefaultSaveConfigurableProducts::class),
    SaveSimpleProductCategoryAssociations::class => get(DefaultSaveSimpleProductCategoryAssociations::class),
    SaveSimpleProducts::class => get(DefaultSaveSimpleProducts::class),
];