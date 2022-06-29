<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Traits\HelperTraits;

use WikibaseSolutions\CypherDSL\HasProperties;
use WikibaseSolutions\CypherDSL\PropertyMap;

/**
 * This trait provides a default implementation to satisfy the "HasProperties" interface.
 *
 * @see HasProperties
 */
trait HasPropertiesTrait
{
    use ErrorTrait;

    /**
     * @var PropertyMap|null The properties this object has
     */
    private ?PropertyMap $properties = null;

    /**
     * @inheritDoc
     */
    public function withProperty(string $key, $value): self
    {
        if ($this->properties === null) {
            $this->properties = new PropertyMap();
        }

        $this->properties->addProperty($key, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withProperties($properties): self
    {
        self::assertClass('properties', [PropertyMap::class, 'array'], $properties);

        $properties = is_array($properties) ? new PropertyMap($properties) : $properties;

        if ($this->properties === null) {
            $this->properties = $properties;
        } else {
            $this->properties->mergeWith($properties);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProperties(): ?PropertyMap
    {
        return $this->properties;
    }
}
