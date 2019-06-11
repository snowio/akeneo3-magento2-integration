<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\ProductModelData as AkeneoConfigurable;
use SnowIO\Akeneo3Magento2\ProductModelMapper;
use SnowIO\Akeneo3Magento2\CustomAttributeMapper;
use SnowIO\Magento2DataModel\CustomAttribute;
use SnowIO\Magento2DataModel\CustomAttributeSet;
use SnowIO\Magento2DataModel\ProductData;
use SnowIO\Magento2DataModel\ProductTypeId;
use SnowIO\Magento2DataModel\ProductVisibility;

class ProductModelMapperTest extends TestCase
{
    public function testMap()
    {
        $akeneoVariant = $this->getAkeneoConfigurableWithProduct();
        $mapper = ProductModelMapper::create();
        $actual = $mapper($akeneoVariant);
        $expected = ProductData::of('abc123', 'abc123')
            ->withAttributeSetCode('default')
            ->withTypeId(ProductTypeId::CONFIGURABLE)
            ->withVisibility(ProductVisibility::CATALOG_SEARCH)
            ->withCustomAttributes(CustomAttributeSet::of([
                CustomAttribute::of('size', 'large'),
                CustomAttribute::of('color', 'blue'),
                CustomAttribute::of('product_title', 'ABC 123 Product Variant 1'),
            ]));
        self::assertEquals($expected->toJson(), $actual->toJson());
        self::assertTrue($expected->equals($actual));
        $akeneoVariant = $this->getAkeneoConfigurableWithoutProduct();
        $mapper = ProductModelMapper::create();
        $actual = $mapper($akeneoVariant);
        $expected = ProductData::of('abc123', 'abc123')
            ->withTypeId(ProductTypeId::CONFIGURABLE)
            ->withVisibility(ProductVisibility::CATALOG_SEARCH)
            ->withCustomAttributes(CustomAttributeSet::of([
                CustomAttribute::of('size', 'large'),
                CustomAttribute::of('color', 'blue'),
                CustomAttribute::of('product_title', 'ABC 123 Product Variant 1'),
            ]));
        self::assertEquals($expected->toJson(), $actual->toJson());
        self::assertTrue($expected->equals($actual));
    }


    public function testMapWithCustomMappers()
    {
        $akeneoVariant = $this->getAkeneoConfigurableWithProduct();
        $mapper = ProductModelMapper::create();
        $mapper = $mapper->withCustomAttributeTransform(
            CustomAttributeMapper::create()
                ->withCurrency('gbp')
                ->getTransform()
        );

        $actual = $mapper($akeneoVariant);
        $expected = ProductData::of('abc123', 'abc123')
            ->withAttributeSetCode('default')
            ->withTypeId(ProductTypeId::CONFIGURABLE)
            ->withVisibility(ProductVisibility::CATALOG_SEARCH)
            ->withCustomAttributes(
                CustomAttributeSet::of([
                    CustomAttribute::of('size', 'large'),
                    CustomAttribute::of('color', 'blue'),
                    CustomAttribute::of('product_title', 'ABC 123 Product Variant 1'),
                    CustomAttribute::of('price', '40.48'),
                ])
            );
        self::assertEquals($expected->toJson(), $actual->toJson());
        self::assertTrue($expected->equals($actual));
    }

    //@todo are we really using the product field??
    private function getAkeneoConfigurableWithProduct(): AkeneoConfigurable
    {
        return AkeneoConfigurable::fromJson([
            'code' => 'abc123',
            'sku' => 'abc123_v1',
            'family_variant' => [
                "code" => "by_colour_size_width",
                "family" => "main",
                "labels" => [
                    "en_GB" => "By Colour, Size, Width"
                ],
                "variant_axes" => [
                    "1" => [
                        "color"
                    ],
                    "2" => [
                        "size",
                    ]
                ],
                "variant_attributes" => [
                    "1" => [
                        "colour_rgb_config"
                    ],
                    "2" => [
                        "sku",
                        "prezola_sku",
                        "title",
                        "mpn",
                        "ean_upc",
                        "price_chargeable",
                        "list_price",
                        "size_sml_config",
                        "width_num_config"
                    ]
                ]
            ],
            'parent' => null,
            'products' => [
                'abc123' => [
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
                    'group' => null,
                    'localizations' => [],
                    'enabled' => true,
                    '@timestamp' => 1508491122,
                ]
            ],
            'channel' => 'main',
            'axis' => ['color', 'size'],
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'attribute_values' => [
                'size' => 'large',
                'color' => 'blue',
                'product_title' => 'ABC 123 Product Variant 1',
                'price' => [
                    'gbp' => '40.48',
                    'euro' => '40.59'
                ]
            ],
            'group' => null,
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]);
    }

    private function getAkeneoConfigurableWithoutProduct(): AkeneoConfigurable
    {
        return AkeneoConfigurable::fromJson([
            'code' => 'abc123',
            'sku' => 'abc123_v1',
            'channel' => 'main',
            'parent' => null,
            'family_variant' => [
                "code" => "by_colour_size_width",
                "family" => "main",
                "labels" => [
                    "en_GB" => "By Colour, Size, Width"
                ],
                "variant_axes" => [
                    "1" => [
                        "color"
                    ],
                    "2" => [
                        "size",
                    ]
                ],
                "variant_attributes" => [
                    "1" => [
                        "colour_rgb_config"
                    ],
                    "2" => [
                        "sku",
                        "prezola_sku",
                        "title",
                        "mpn",
                        "ean_upc",
                        "price_chargeable",
                        "list_price",
                        "size_sml_config",
                        "width_num_config"
                    ]
                ]
            ],
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'attribute_values' => [
                'size' => 'large',
                'color' => 'blue',
                'product_title' => 'ABC 123 Product Variant 1',
                'price' => [
                    'gbp' => '40.48',
                    'euro' => '40.59'
                ]
            ],
            'group' => null,
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]);
    }
}
