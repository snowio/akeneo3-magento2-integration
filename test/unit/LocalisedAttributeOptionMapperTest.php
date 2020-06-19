<?php
declare(strict_types=1);

namespace SnowIO\Akeneo3Magento2\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\Akeneo3DataModel\AttributeOptionIdentifier;
use SnowIO\Akeneo3DataModel\InternationalizedString;
use SnowIO\Akeneo3DataModel\LocalizedString;
use SnowIO\Akeneo3Magento2\AttributeOptionMapper;
use SnowIO\Akeneo3Magento2\LocalisedAttributeOptionMapper;
use SnowIO\Magento2DataModel\AttributeOption as Magento2AttributeOption;
use SnowIO\Magento2DataModel\AttributeOptionStoreLabel;
use SnowIO\Magento2DataModel\AttributeOptionStoreLabelSet;

class LocalisedAttributeOptionMapperTest extends TestCase
{

    public function testMapWithLocalStoreMap()
    {
        $input = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'size-large'))
            ->withLabels(InternationalizedString::fromJson([
                'en_GB' => "Large", "fr_FR" => "Grand", "de_DE" => "Gross"
            ]));

        $mapper = LocalisedAttributeOptionMapper::withLocaleStoreMap([
            "en_GB" => "en_gb", "fr_FR" => "fr_fr", "de_DE" => "de_de"
        ], "en_GB");

        $actual = $mapper($input);
        $expected = Magento2AttributeOption::of('size', 'large', 'Large')
            ->withStoreLabels(AttributeOptionStoreLabelSet::of([
                AttributeOptionStoreLabel::of("en_gb", "Large"),
                AttributeOptionStoreLabel::of("fr_fr", "Grand"),
                AttributeOptionStoreLabel::of("de_de", "Gross"),
            ]));

        self::assertTrue($expected->equals($actual));
    }
}
