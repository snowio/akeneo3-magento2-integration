<?php
namespace SnowIO\Akeneo3Magento2\Integrations\Functions;

interface MappingFunction
{
    public function __invoke(array $eventJson);
}