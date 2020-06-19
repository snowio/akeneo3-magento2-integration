<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2;

use SnowIO\Akeneo3DataModel\LocalizedString;
use SnowIO\Magento2DataModel\AttributeOption as Magento2AttributeOption;
use SnowIO\Akeneo3DataModel\AttributeOption as Akeneo3AttributeOption;
use SnowIO\Magento2DataModel\AttributeOptionStoreLabel;
use SnowIO\Magento2DataModel\AttributeOptionStoreLabelSet;

final class LocalisedAttributeOptionMapper extends DataMapper
{
    /**
     * @param string[] $localeStoreMap
     * @param string $defaultLocale
     * @return LocalisedAttributeOptionMapper
     */
    public static function withLocaleStoreMap(array $localeStoreMap, string $defaultLocale): self
    {
        return new self($localeStoreMap, $defaultLocale);
    }

    public function __invoke(Akeneo3AttributeOption $attributeOption): Magento2AttributeOption
    {
        $labels = $attributeOption->getLabels();
        $storeLabels = AttributeOptionStoreLabelSet::of(array_map([$this, 'getStoreLabel'], iterator_to_array($labels)));
        /** @var Magento2AttributeOption $magentoAttributeOption */
        $magentoAttributeOption = ($this->attributeOptionMapper)($attributeOption);

        return $magentoAttributeOption
            ->withStoreLabels($storeLabels);
    }

    private function getStoreLabel(LocalizedString $label)
    {
        /** @var string|null $storeCode */
        $storeCode = $this->localeStoreMap[$label->getLocale()] ?? null;

        if (!$storeCode) {
            throw new \InvalidArgumentException("Unknown Store Code '$storeCode'");
        }

        return AttributeOptionStoreLabel::of($storeCode, $label->getValue());
    }


    private $localeStoreMap;
    private $attributeOptionMapper;

    private function __construct(array $localeStoreMap, string $defaultLocale)
    {
        $this->localeStoreMap = $localeStoreMap;
        $this->attributeOptionMapper = AttributeOptionMapper::withDefaultLocale($defaultLocale);
    }
}
