<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2\MessageMapper;

use SnowIO\Transform\Transform;
use SnowIO\Akeneo3DataModel\Event\AttributeOptionDeletedEvent;
use SnowIO\Akeneo3DataModel\Event\AttributeOptionSavedEvent;
use SnowIO\Akeneo3DataModel\Event\EntityStateEvent;
use SnowIO\Magento2DataModel\AttributeOption;
use SnowIO\Magento2DataModel\Command\Command;
use SnowIO\Magento2DataModel\Command\DeleteAttributeOptionCommand;
use SnowIO\Magento2DataModel\Command\SaveAttributeOptionCommand;

final class AttributeOptionMessageMapper extends MessageMapperWithDeleteSupport
{
    public static function withAttributeOptionTransform(Transform $transform): self
    {
        return new self($transform);
    }

    protected function resolveEntitySavedEvent($event): EntityStateEvent
    {
        if (\is_array($event)) {
            $event = AttributeOptionSavedEvent::fromJson($event);
        } elseif (!$event instanceof AttributeOptionSavedEvent) {
            throw new \InvalidArgumentException;
        }
        return $event;
    }

    protected function resolveEntityDeletedEvent($event): EntityStateEvent
    {
        if (\is_array($event)) {
            $event = AttributeOptionDeletedEvent::fromJson($event);
        } elseif (!$event instanceof AttributeOptionDeletedEvent) {
            throw new \InvalidArgumentException;
        }
        return $event;
    }

    /**
     * @param AttributeOption $magentoEntityData
     */
    protected function getRepresentativeValueForDiff($magentoEntityData): string
    {
        return \json_encode($magentoEntityData->toJson());
    }

    /**
     * @param AttributeOption $magentoEntityData
     */
    protected function getMagentoEntityIdentifier($magentoEntityData): string
    {
        return "{$magentoEntityData->getAttributeCode()} {$magentoEntityData->getValue()}";
    }

    /**
     * @param AttributeOption $magentoEntityData
     */
    protected function createSaveEntityCommand($magentoEntityData): Command
    {
        return SaveAttributeOptionCommand::of($magentoEntityData);
    }

    protected function createDeleteEntityCommand(string $magentoEntityIdentifier): Command
    {
        list($attributeCode, $value) = \explode(' ', $magentoEntityIdentifier, $limit = 2);
        return DeleteAttributeOptionCommand::of($attributeCode, $value);
    }
}
