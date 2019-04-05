<?php

namespace Spatie\Calendar\Components;

use Spatie\Calendar\Builders\ComponentBuilder;
use Spatie\Calendar\ComponentPayload;
use Spatie\Calendar\Exceptions\InvalidComponent;
use Spatie\Calendar\PropertyTypes\PropertyType;

abstract class Component
{
    abstract public function getComponentType(): string;

    abstract public function getRequiredProperties(): array;

    abstract public function getPayload(): ComponentPayload;

    public function toString(): string
    {
        $payload = $this->getPayload();

        $this->ensureRequiredPropertiesAreSet($payload);

        $builder = new ComponentBuilder($payload);

        return $builder->build();
    }

    protected function ensureRequiredPropertiesAreSet(ComponentPayload $componentPayload)
    {
        $requiredProperties = $this->getRequiredProperties();

        $providedProperties = array_map(function (PropertyType $property) {
            return $property->getName();
        }, $componentPayload->getProperties());

        $intersection = array_intersect($requiredProperties, $providedProperties);

        if (count($intersection) !== count($requiredProperties)) {
            $notProvidedProperties = array_diff($requiredProperties, $intersection);

            throw InvalidComponent::requiredPropertyMissing($notProvidedProperties, $this);
        }
    }
}
