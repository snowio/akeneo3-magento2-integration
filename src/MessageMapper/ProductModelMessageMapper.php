<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2\MessageMapper;

use Joshdifabio\Transform\Transform;
use SnowIO\Akeneo3DataModel\Event\ProductDeletedEvent;
use SnowIO\Akeneo3DataModel\Event\ProductSavedEvent;
use SnowIO\Akeneo3DataModel\Event\EntityStateEvent;
use SnowIO\Akeneo3DataModel\Event\ProductModelDeletedEvent;
use SnowIO\Akeneo3DataModel\Event\ProductModelSavedEvent;
use SnowIO\Akeneo3Magento2\ProductModelMapper;
use SnowIO\Magento2DataModel\Command\Command;
use SnowIO\Magento2DataModel\ProductData;
use SnowIO\Magento2DataModel\Command\DeleteProductCommand;
use SnowIO\Magento2DataModel\Command\SaveProductCommand;

final class ProductModelMessageMapper extends MessageMapperWithDeleteSupport
{
    public static function create(): self
    {
        return new self(ProductModelMapper::create()->getTransform());
    }

    public function withVariantGroupTransform(Transform $transform): self
    {
        return new self($transform);
    }

    protected function resolveEntitySavedEvent($event): EntityStateEvent
    {
        if (\is_array($event)) {
            $event = ProductModelSavedEvent::fromJson($event);
        } elseif (!$event instanceof ProductSavedEvent) {
            throw new \InvalidArgumentException;
        }
        return $event;
    }

    protected function resolveEntityDeletedEvent($event): EntityStateEvent
    {
        if (\is_array($event)) {
            $event = ProductModelDeletedEvent::fromJson($event);
        } elseif (!$event instanceof ProductDeletedEvent) {
            throw new \InvalidArgumentException;
        }
        return $event;
    }

    /**
     * @param ProductData $magentoEntityData
     */
    protected function getRepresentativeValueForDiff($magentoEntityData): string
    {
        return \json_encode($magentoEntityData->toJson());
    }

    /**
     * @param ProductData $magentoEntityData
     */
    protected function getMagentoEntityIdentifier($magentoEntityData): string
    {
        return $magentoEntityData->getSku();
    }

    /**
     * @param ProductData $magentoEntityData
     */
    protected function createSaveEntityCommand($magentoEntityData): Command
    {
        return SaveProductCommand::of($magentoEntityData);
    }

    protected function createDeleteEntityCommand(string $magentoEntityIdentifier): Command
    {
        return DeleteProductCommand::of($magentoEntityIdentifier);
    }
}
