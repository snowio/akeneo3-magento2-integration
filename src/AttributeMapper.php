<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2;

use SnowIO\Akeneo3DataModel\AttributeData as Akeneo3AttributeData;
use SnowIO\Akeneo3DataModel\AttributeType;
use SnowIO\Magento2DataModel\AttributeData as Magento2AttributeData;
use SnowIO\Magento2DataModel\FrontendInput;

final class AttributeMapper extends DataMapper
{
    public static function withDefaultLocale(string $defaultLocale): self
    {
        $attributeMapper = new self;
        $attributeMapper->defaultLocale = $defaultLocale;
        return $attributeMapper;
    }

    public function __invoke(Akeneo3AttributeData $attributeData): Magento2AttributeData
    {
        $frontendInput = ($this->typeToFrontendInputMapper)($attributeData->getType());
        $defaultFrontendLabel = $attributeData->getLabel($this->defaultLocale) ?? $attributeData->getCode();
        return Magento2AttributeData::of($attributeData->getCode(), $frontendInput, $defaultFrontendLabel);
    }

    public function withTypeToFrontendInputMapper(callable $fn): self
    {
        $result = clone $this;
        $result->typeToFrontendInputMapper = $fn;
        return $result;
    }

    public static function getDefaultTypeToFrontendInputMapper(): callable
    {
        $typeToFrontendInputMap = [
            AttributeType::IDENTIFIER => FrontendInput::TEXT,
            AttributeType::SIMPLESELECT => FrontendInput::SELECT,
            AttributeType::BOOLEAN => FrontendInput::BOOLEAN,
            AttributeType::NUMBER => FrontendInput::TEXT,
            AttributeType::PRICE_COLLECTION => FrontendInput::PRICE,
            AttributeType::DATE => FrontendInput::DATE,
            AttributeType::TEXT => FrontendInput::TEXT,
            AttributeType::TEXTAREA => FrontendInput::TEXTAREA,
            AttributeType::MULTISELECT => FrontendInput::MULTISELECT,
            AttributeType::ASSETS_COLLECTION => FrontendInput::MEDIA_IMAGE
        ];
        return function (string $akeneoType) use ($typeToFrontendInputMap) {
            return $typeToFrontendInputMap[$akeneoType] ?? FrontendInput::TEXT;
        };
    }

    /** @var callable */
    private $typeToFrontendInputMapper;
    private $defaultLocale;

    private function __construct()
    {
        $this->typeToFrontendInputMapper = self::getDefaultTypeToFrontendInputMapper();
    }
}
