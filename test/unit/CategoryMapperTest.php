<?php
declare(strict_types=1);

namespace SnowIO\Akeneo3Magento2\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\CategoryData as AkeneoCategoryData;
use SnowIO\Akeneo3DataModel\CategoryPath;
use SnowIO\Akeneo3DataModel\LocalizedString;
use SnowIO\Akeneo3Magento2\CategoryMapper;
use SnowIO\Magento2DataModel\CategoryData as Magento2CategoryData;

class CategoryMapperTest extends TestCase
{

    public function testMap()
    {
        $akeneoCategoryData = AkeneoCategoryData::of(CategoryPath::of(['mens', 't_shirts']))
            ->withLabel(LocalizedString::of('Mens T-Shirts', 'en_GB'));
        $expected = Magento2CategoryData::of('t_shirts', 'Mens T-Shirts')
            ->withParentCode('mens');
        $mapper = CategoryMapper::withDefaultLocale('en_GB');
        $actual = $mapper($akeneoCategoryData);
        self::assertTrue($expected->equals($actual));
    }

}
