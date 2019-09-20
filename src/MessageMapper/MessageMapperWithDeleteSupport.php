<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Magento2\MessageMapper;

use SnowIO\Transform\Pipeline;
use SnowIO\Transform\Diff;
use SnowIO\Transform\Filter;
use SnowIO\Transform\MapElements;
use SnowIO\Transform\Transform;
use SnowIO\Akeneo3DataModel\Event\EntityStateEvent;
use SnowIO\Magento2DataModel\Command\Command;

abstract class MessageMapperWithDeleteSupport extends MessageMapper
{
    public function transformAkeneoSavedEventToMagentoDeleteCommands($entitySavedEvent): \Iterator
    {
        $entitySavedEvent = $this->resolveEntitySavedEvent($entitySavedEvent);

        return $this->transformAkeneoDataToMagentoDeleteCommands()
            ->then(MapElements::via(function (Command $command) use ($entitySavedEvent) {
                return $command->withTimestamp($entitySavedEvent->getTimestamp());
            }))
            ->applyTo([[$entitySavedEvent->getPreviousEntityData()], [$entitySavedEvent->getCurrentEntityData()]]);
    }

    public function transformAkeneoDeletedEventToMagentoDeleteCommands($entityDeletedEvent): \Iterator
    {
        $entityDeletedEvent = $this->resolveEntityDeletedEvent($entityDeletedEvent);

        return $this->transformAkeneoDataToMagentoDeleteCommands()
            ->then(MapElements::via(function (Command $command) use ($entityDeletedEvent) {
                return $command->withTimestamp($entityDeletedEvent->getTimestamp());
            }))
            ->applyTo([[$entityDeletedEvent->getPreviousEntityData()]]);
    }

    public function transformAkeneoDataToMagentoDeleteCommands(): Transform
    {
        $elementTransform = $this->dataTransform->then(MapElements::via(function ($magentoEntityData) {
            return $this->getMagentoEntityIdentifier($magentoEntityData);
        }));

        return Pipeline::of(
            Filter::notEqualTo([null]),
            MapElements::via([$elementTransform, 'applyTo']),
            Diff::create(),
            MapElements::via(function ($magentoEntityIdentifier) {
                return $this->createDeleteEntityCommand($magentoEntityIdentifier);
            })
        );
    }

    abstract protected function resolveEntityDeletedEvent($event): EntityStateEvent;

    abstract protected function createDeleteEntityCommand(string $magentoEntityIdentifier): Command;
}
