<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2\MessageMapper;

use SnowIO\Transform\Transform;
use SnowIO\Akeneo3DataModel\Event\EntityStateEvent;
use SnowIO\Akeneo3DataModel\Event\FamilySavedEvent;
use SnowIO\Magento2DataModel\AttributeSetData;
use SnowIO\Magento2DataModel\Command\Command;
use SnowIO\Magento2DataModel\Command\SaveAttributeSetCommand;

final class FamilyMessageMapper extends MessageMapper
{
    public static function withFamilyTransform(Transform $transform): self
    {
        return new self($transform);
    }

    protected function resolveEntitySavedEvent($event): EntityStateEvent
    {
        if (\is_array($event)) {
            $event = FamilySavedEvent::fromJson($event);
        } elseif (!$event instanceof FamilySavedEvent) {
            throw new \InvalidArgumentException;
        }
        return $event;
    }

    /**
     * @param AttributeSetData $magentoEntityData
     */
    protected function getRepresentativeValueForDiff($magentoEntityData): string
    {
        return \json_encode($magentoEntityData->toJson());
    }

    /**
     * @param AttributeSetData $magentoEntityData
     */
    protected function getMagentoEntityIdentifier($magentoEntityData): string
    {
        return $magentoEntityData->getCode();
    }

    /**
     * @param AttributeSetData $magentoEntityData
     */
    protected function createSaveEntityCommand($magentoEntityData): Command
    {
        return SaveAttributeSetCommand::of($magentoEntityData);
    }
}
