<?php
namespace SnowIO\Akeneo3Magento2;

use function SnowIO\Transform\identity;
use SnowIO\Transform\MapElements;
use SnowIO\Transform\MapValues;
use SnowIO\Transform\Transform;
use SnowIO\Transform\WithKeys;

abstract class DataMapper
{
    public function getTransform(): Transform
    {
        return MapElements::via($this);
    }

    public function getValueTransform(): Transform
    {
        return MapValues::via($this);
    }

    public function getKvTransform(): Transform
    {
        return WithKeys::of(identity())->then(MapValues::via($this));
    }
}
