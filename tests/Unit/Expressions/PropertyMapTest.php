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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\PropertyMap
 */
class PropertyMapTest extends TestCase
{
    use TestHelper;

    public function testEmpty()
    {
        $propertyMap = new PropertyMap([]);

        $this->assertSame("{}", $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNumericalKeysData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testNumericalKeys(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideStringKeysData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testStringKeys(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNestedPropertyMapsData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testNestedPropertyMaps(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    public function provideNumericalKeysData(): array
    {
        return [
        [[$this->getExpressionMock("'a'", $this)], "{`0`: 'a'}"],
        [[$this->getExpressionMock("'a'", $this), $this->getExpressionMock("'b'", $this)], "{`0`: 'a', `1`: 'b'}"]
        ];
    }

    public function provideStringKeysData(): array
    {
        return [
        [['a' => $this->getExpressionMock("'a'", $this)], "{a: 'a'}"],
        [['a' => $this->getExpressionMock("'a'", $this), 'b' => $this->getExpressionMock("'b'", $this)], "{a: 'a', b: 'b'}"],
        [['a' => $this->getExpressionMock("'b'", $this)], "{a: 'b'}"],
        [[':' => $this->getExpressionMock("'a'", $this)], "{`:`: 'a'}"]
        ];
    }

    public function provideNestedPropertyMapsData()
    {
        return [
        [['a' => new PropertyMap([])], "{a: {}}"],
        [['a' => new PropertyMap(['a' => new PropertyMap(['a' => $this->getExpressionMock("'b'", $this)])])], "{a: {a: {a: 'b'}}}"],
        [['a' => new PropertyMap(['b' => $this->getExpressionMock("'c'", $this)]), 'b' => $this->getExpressionMock("'d'", $this)], "{a: {b: 'c'}, b: 'd'}"]
        ];
    }
}