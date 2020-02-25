<?php
namespace SnowIO\Akeneo3Magento2\Integrations;

use SnowIO\Akeneo3Magento2\MessageMapper\AttributeMessageMapper;
use SnowIO\Akeneo3Magento2\MessageMapper\AttributeOptionMessageMapper;
use SnowIO\Akeneo3Magento2\MessageMapper\CategoryMessageMapper;
use SnowIO\Akeneo3Magento2\MessageMapper\FamilyMessageMapper;
use SnowIO\Transform\Transform;

interface AkeneoMagentoConfiguration
{
    public function getCategoryMessageMapper(string $channel): CategoryMessageMapper;
    public function getAttributeSetMessageMapper(): FamilyMessageMapper;
    public function getAttributeOptionMessageMapper(): AttributeOptionMessageMapper;
    public function getAttributeMessageMapper(): AttributeMessageMapper;
    public function transformProductSavedEventToMagentoProductIterables($channel): Transform;
    public function transformProductModelSavedEventToMagentoProductIterables($channel): Transform;
    public function transformProductDataToMagentoProducts($channel): Transform;
    public function transformProductModelDataToMagentoProducts($channel): Transform;
}