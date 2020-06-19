<?php

namespace SnowIO\Akeneo3Magento2\Integrations;

use SnowIO\Transform\Filter;
use SnowIO\Transform\FlatMapElements;
use SnowIO\Transform\Kv;
use SnowIO\Transform\MapElements;
use SnowIO\Transform\MapValues;
use SnowIO\Transform\Pipeline;
use SnowIO\Transform\Transform;
use SnowIO\Akeneo3DataModel\AttributeValue;
use SnowIO\Akeneo3DataModel\FamilyAttributeData;
use SnowIO\Akeneo3Magento2\AttributeMapper;
use SnowIO\Akeneo3Magento2\AttributeOptionMapper;
use SnowIO\Akeneo3Magento2\CategoryMapper;
use SnowIO\Akeneo3Magento2\CustomAttributeMapper;
use SnowIO\Akeneo3Magento2\FamilyMapper;
use SnowIO\Akeneo3Magento2\MessageMapper\AttributeMessageMapper;
use SnowIO\Akeneo3Magento2\MessageMapper\AttributeOptionMessageMapper;
use SnowIO\Akeneo3Magento2\MessageMapper\CategoryMessageMapper;
use SnowIO\Akeneo3DataModel\CategoryData as AkeneoCategoryData;
use SnowIO\Akeneo3Magento2\MessageMapper\FamilyMessageMapper;
use SnowIO\Magento2DataModel\AttributeScope;
use SnowIO\Magento2DataModel\AttributeSet\AttributeData;
use SnowIO\Magento2DataModel\AttributeSet\AttributeGroupData;
use SnowIO\Magento2DataModel\CategoryData as MagentoCategoryData;
use SnowIO\Akeneo3DataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\Magento2DataModel\AttributeData as MagentoAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\Akeneo3DataModel\AttributeGroup as Akeneo2AttributeGroup;
use SnowIO\Magento2DataModel\AttributeSet\AttributeDataSet;
use SnowIO\Magento2DataModel\CustomAttribute;

class DefaultAkeneoMagentoConfiguration implements AkeneoMagentoConfiguration
{
    const DEFAULT_LOCALE = 'en_GB';

    public function getCategoryMessageMapper(string $channel): CategoryMessageMapper
    {
        if (!isset($this->categoryMessageMappers[$channel])) {
            $categoryTransform = Pipeline::of(
                CategoryMapper::withDefaultLocale(self::DEFAULT_LOCALE)->getKvTransform(),
                MapElements::via(Kv::unpack(function (AkeneoCategoryData $akeneoCategoryData, MagentoCategoryData $magentoCategoryData) {
                    $fhCategoryId = sanitizeCategoryId($akeneoCategoryData->getCode());
                    return $magentoCategoryData
                        ->withCustomAttribute(CustomAttribute::of('fredhopper_category_id', $fhCategoryId));
                })),
                FlatMapElements::via(function (MagentoCategoryData $magentoCategoryData) use ($channel) {
                    yield $magentoCategoryData; // admin scope
                    foreach (MagentoStoreCode::getAll($channel) as $storeCode) {
                        yield $magentoCategoryData->withStoreCode($storeCode);
                    }
                })
            );
            $this->categoryMessageMappers[$channel] = CategoryMessageMapper::withCategoryTransform($categoryTransform);
        }
        return $this->categoryMessageMappers[$channel];
    }


    public function getAttributeSetMessageMapper(): FamilyMessageMapper
    {
        if (!isset($this->attributeSetMessageMapper)) {
            $attributeTransform = Pipeline::of(
                Filter::by(function (FamilyAttributeData $familyAttributeData) {
                    return !self::customAttributeIsBlacklisted($familyAttributeData->getCode());
                }),
                FamilyMapper::getDefaultAttributeTransform()
            );
            $attributeGroupTransform = Pipeline::of(
                self::getDefaultAttributeGroupTransform(self::DEFAULT_LOCALE),
                MapElements::via(function (AttributeGroupData $attributeGroupData) {
                    $akeneoSortOrder = $attributeGroupData->getSortOrder();
                    $magentoSortOrder = $akeneoSortOrder + 100;
                    $attributeGroupData = self::applyStaticAttributesToAttributeGroup($attributeGroupData);
                    return $attributeGroupData->withSortOrder($magentoSortOrder);
                })
            );
            $familyTransform = FamilyMapper::withDefaultLocale(self::DEFAULT_LOCALE)
                ->withAttributeTransform($attributeTransform)
                ->withAttributeGroupTransform($attributeGroupTransform)
                ->getTransform();
            $this->attributeSetMessageMapper = FamilyMessageMapper::withFamilyTransform($familyTransform);
        }
        return $this->attributeSetMessageMapper;
    }

    private function getDefaultAttributeGroupTransform(string $defaultLocale): Transform
    {
        return MapElements::via(Kv::unpack(
            function (Akeneo2AttributeGroup $attributeGroup, AttributeDataSet $attributes) use ($defaultLocale) {
                $attributeGroupName = $attributeGroup->getLabel($defaultLocale) . ' (Akeneo)';
                $attributeGroupCode = $attributeGroup->getCode() . '_akeneo';
                return AttributeGroupData::of($attributeGroupCode, $attributeGroupName)
                    ->withSortOrder($attributeGroup->getSortOrder())
                    ->withAttributes($attributes);
            }
        ));
    }

    public function getAttributeOptionMessageMapper(): AttributeOptionMessageMapper
    {
        if (!isset($this->attributeOptionMessageMapper)) {
            $attributeOptionTransform = Pipeline::of(
                Filter::by(function (AkeneoAttributeOption $akeneoAttributeOption) {
                    $attributeBlacklisted = self::customAttributeIsBlacklisted($akeneoAttributeOption->getAttributeCode());
                    $attributeOptionsBlacklisted = self::customAttributeOptionsIsBlacklisted($akeneoAttributeOption->getAttributeCode());
                    return !$attributeBlacklisted && !$attributeOptionsBlacklisted;
                }),
                AttributeOptionMapper::withDefaultLocale(self::DEFAULT_LOCALE)->getTransform()
            );
            $this->attributeOptionMessageMapper = AttributeOptionMessageMapper::withAttributeOptionTransform($attributeOptionTransform);
        }
        return $this->attributeOptionMessageMapper;
    }

    public function getAttributeMessageMapper(): AttributeMessageMapper
    {
        if (!isset($this->attributeMessageMapper)) {
            $attributeTransform = Pipeline::of(
                Filter::by(function (AkeneoAttributeData $akeneoAttribute) {
                    return !self::customAttributeIsBlacklisted($akeneoAttribute->getCode());
                }),
                AttributeMapper::withDefaultLocale(self::DEFAULT_LOCALE)->getKvTransform(),
                MapElements::via(Kv::unpack(function (AkeneoAttributeData $akeneoAttributeData, MagentoAttributeData $magentoAttributeData) {
                    if (
                        $akeneoAttributeData->isLocalizable()
                        || $akeneoAttributeData->getType() === AkeneoAttributeType::PRICE_COLLECTION
                    ) {
                        $scope = AttributeScope::STORE;
                    } else {
                        $scope = AttributeScope::GLOBAL;
                    }
                    return $magentoAttributeData->withScope($scope);
                }))
            );
            $this->attributeMessageMapper = AttributeMessageMapper::withAttributeTransform($attributeTransform);
        }

        return $this->attributeMessageMapper;
    }

    public function transformProductSavedEventToMagentoProductIterables($channel): Transform
    {
        return Pipeline::of(
            FlatMapElements::via(function (ProductSavedEvent $event) {
                yield Kv::of('current', [$event->getCurrent()]);
                yield Kv::of('previous', [$event->getPrevious()]);
            }),
            Filter::valueNotEqualTo([null]),
            MapValues::via([self::transformProductDataToMagentoProducts($channel), 'applyTo'])
        );
    }

    public function transformProductModelSavedEventToMagentoProductIterables($channel): Transform
    {
        return Pipeline::of(
            FlatMapElements::via(function (Datalake $event) {
                yield Kv::of('current', [$event->getCurrent()]);
                yield Kv::of('previous', [$event->getPrevious()]);
            }),
            Filter::valueNotEqualTo([null]),
            MapValues::via([self::transformProductModelDataToMagentoProducts($channel), 'applyTo'])
        );
    }

    public function transformProductDataToMagentoProducts($channel): Transform
    {
        if (!isset($this->productTransforms[$channel])) {
            $this->productTransforms[$channel] = ProductTransform::ofChannel($channel)
                ->withCustomAttributeTransform(self::getCustomerAttributeTransform());
        }
        return $this->productTransforms[$channel];
    }

    public function transformProductModelDataToMagentoProducts($channel): Transform
    {
        if (!isset($this->productModelTransforms[$channel])) {
            $this->productModelTransforms[$channel] = ProductModelTransform::ofChannel($channel)
                ->withProductTransform(self::transformProductDataToMagentoProducts($channel))
                ->withCustomAttributeTransform(self::getCustomerAttributeTransform());
        }

        return $this->productModelTransforms[$channel];
    }

    private $productModelTransforms;
    private $productTransforms;
    private $categoryMessageMappers;
    private $attributeMessageMapper;
    private $attributeOptionMessageMapper;
    private $attributeSetMessageMapper;

    private function getCustomerAttributeTransform(): Transform
    {
        return Pipeline::of(
            Filter::by(function (AttributeValue $attributeValue) {
                return !self::customAttributeIsBlacklisted($attributeValue->getAttributeCode());
            }),
            CustomAttributeMapper::create()->getTransform()
        );
    }

    private function customAttributeIsBlacklisted($attributeCode): bool
    {
        if (\in_array($attributeCode, $this->blacklistedAttributeCodes, $strict = true)) {
            return true;
        }

        foreach ($this->blacklistedAttributeCodesPrefixes as $attributeCodePrefix) {
            if (0 === \strpos($attributeCode, $attributeCodePrefix)) {
                return true;
            }
        }

        return false;
    }

    private function customAttributeOptionsIsBlacklisted(string $attributeCode): bool
    {
        if (\in_array($attributeCode, $this->blacklistedAttributeCodesForOptions, $strict = true)) {
            return true;
        }
        return false;
    }

    /**
     * @param AttributeGroupData $attributeGroupData
     * @return AttributeGroupData
     *
     * Adds static attributes (must exist in Magento) into an attribute group
     * Does not remove the static attribute from other groups if already present from Akeneo
     * Remove the attribute from the family in Akeneo before creating a static mapping here
     */
    public function applyStaticAttributesToAttributeGroup(AttributeGroupData $attributeGroupData): AttributeGroupData
    {
        if (isset($this->attributeGroupAttributeCodes[$attributeGroupData->getCode()])) {
            $sortOrder = \array_reduce(\iterator_to_array($attributeGroupData->getAttributes()), function ($carry, $item) {
                    /** @var AttributeData $item */
                    return max($carry, $item->getSortOrder());
                }, 0) + 1;
            foreach ($this->attributeGroupAttributeCodes[$attributeGroupData->getCode()] as $attributeCode) {
                $attributeGroupData = $attributeGroupData
                    ->withAttribute(AttributeData::of($attributeCode)
                        ->withSortOrder($sortOrder)
                    );
                $sortOrder++;
            }
        }
        return $attributeGroupData;
    }

    private $attributeGroupAttributeCodes = [];


    private $blacklistedAttributeCodes = [];

    private $blacklistedAttributeCodesPrefixes = [];

    private $blacklistedAttributeCodesForOptions = [];
}