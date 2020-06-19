<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2;

use SnowIO\Magento2DataModel\AttributeOption as Magento2AttributeOption;
use SnowIO\Akeneo3DataModel\AttributeOption as Akeneo3AttributeOption;

final class AttributeOptionMapper extends DataMapper
{
    public static function withDefaultLocale(string $defaultLocale): self
    {
        return new self($defaultLocale);
    }

    public function __invoke(Akeneo3AttributeOption $attributeOption): Magento2AttributeOption
    {
        $magentoAttributeOption = Magento2AttributeOption::of(
            $attributeOption->getAttributeCode(),
            $this->obtainOptionCodeFromPrefixedOptionCode($attributeOption),
            $attributeOption->getLabel($this->defaultLocale) ?? $attributeOption->getOptionCode()
        );

        if ($attributeOption->getSortOrder()) {
            return $magentoAttributeOption->withSortOrder((int) $attributeOption->getSortOrder());
        }

        return $magentoAttributeOption;
    }

    private function obtainOptionCodeFromPrefixedOptionCode(Akeneo3AttributeOption $attributeOption)
    {
        $optionCodes = explode('-', $attributeOption->getOptionCode());
        return count($optionCodes) >= 2 && $optionCodes[0] === $attributeOption->getAttributeCode() ?
            implode('-', array_slice($optionCodes, 1)) :
            $attributeOption->getOptionCode();
    }

    private $defaultLocale;

    private function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }
}
