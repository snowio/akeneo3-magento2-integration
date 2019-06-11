<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2;

use SnowIO\Akeneo3DataModel\CategoryData as Akeneo3CategoryData;
use SnowIO\Magento2DataModel\CategoryData as Magento2CategoryData;

final class CategoryMapper extends DataMapper
{
    public static function withDefaultLocale(string $defaultLocale): self
    {
        return new self($defaultLocale);
    }

    public function __invoke(Akeneo3CategoryData $categoryData): Magento2CategoryData
    {
        $code = $categoryData->getCode();
        $name = $categoryData->getLabel($this->defaultLocale);
        $parent = $categoryData->getParent();
        $category = Magento2CategoryData::of($code, $name);
        if ($parent !== null) {
            $category = $category->withParentCode($parent);
        }
        return $category;
    }

    private $defaultLocale;

    private function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }
}
