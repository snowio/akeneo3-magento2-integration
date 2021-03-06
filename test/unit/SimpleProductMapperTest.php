<?php
declare(strict_types = 1);

namespace SnowIO\Akeneo3Magento2\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3Magento2\CustomAttributeMapper;
use SnowIO\Akeneo3Magento2\ProductMapper;
use SnowIO\Akeneo3DataModel\ProductData as AkeneoProduct;
use SnowIO\Magento2DataModel\CustomAttribute;
use SnowIO\Magento2DataModel\CustomAttributeSet;
use SnowIO\Magento2DataModel\ProductData as Magento2ProductData;

class SimpleProductMapperTest extends TestCase
{
    public function testMap()
    {
        $akeneoProduct = $this->getAkeneoProduct();
        $mapper = ProductMapper::create();
        $actual = $mapper($akeneoProduct);
        $expected = Magento2ProductData::of('abc123', 'abc123')
            ->withAttributeSetCode('mens_t_shirts')
            ->withCustomAttributes(CustomAttributeSet::of([
                CustomAttribute::of('size', 'Large'),
                CustomAttribute::of('product_title', 'ABC 123 Product')
            ]));
        self::assertTrue($actual->equals($expected));
    }

    public function testMapWithCustomMappers()
    {
        $akeneoProduct = $this->getAkeneoProduct();
        $mapper = ProductMapper::create()
            ->withAttributeSetCodeMapper(function (string $akeneoFamily = 'default') {
                return "{$akeneoFamily}_modified";
            })
            ->withCustomAttributeTransform(CustomAttributeMapper::create()->withCurrency('gbp')->getTransform());
        $actual = $mapper($akeneoProduct);
        $expected = Magento2ProductData::of('abc123', 'abc123')
            ->withAttributeSetCode('mens_t_shirts_modified')
            ->withCustomAttributes(CustomAttributeSet::of([
                CustomAttribute::of('size', 'Large'),
                CustomAttribute::of('price', '40.48'),
                CustomAttribute::of('product_title', 'ABC 123 Product')
            ]));
        self::assertTrue($actual->equals($expected));
    }

    private function getAkeneoProduct(): AkeneoProduct
    {
        return AkeneoProduct::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
                'product_title' => 'ABC 123 Product',
                'price' => [
                    'gbp' => '40.48',
                    'euro' => '40.59'
                ]
            ],
            'parent' => null,
            'groups' => [],
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]);
    }
}
