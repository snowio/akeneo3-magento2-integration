<?php
namespace SnowIO\Akeneo3Magento2\Integrations\Functions\Implementation;

use SnowIO\Akeneo3Magento2\Integrations\MagentoConfiguration;
use SnowIO\Akeneo3Magento2\Integrations\MessageMapper\AttributeMessageMapper;

class DefaultSaveAttribute
{
    /** @var MagentoConfiguration */
    private $configuration;
    public function __construct(MagentoConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function __invoke(array $eventJson)
    {
        /** @var AttributeMessageMapper $mapper */
        $mapper = $this->configuration->getAttributeMessageMapper();
        return $mapper
            ->transformAkeneoDataToMagentoSaveCommands()
            ->applyTo($eventJson);
    }
}