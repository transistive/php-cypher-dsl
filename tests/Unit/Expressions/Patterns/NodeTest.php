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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Patterns;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Patterns\Node
 */
class NodeTest extends TestCase
{
    public function testEmptyNode()
    {
        $node = new Node();

        $this->assertSame("()", $node->toQuery());
    }

    /**
     * @dataProvider provideOnlyLabelData
     * @param        string $label
     * @param        string $expected
     */
    public function testOnlyLabel(string $label, string $expected)
    {
        $node = new Node();
        $node->withLabel($label);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideOnlyNameData
     * @param        string $name
     * @param        string $expected
     */
    public function testOnlyName(string $name, string $expected)
    {
        $node = new Node();
        $node->named($name);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideOnlyPropertiesData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testOnlyProperties(array $properties, string $expected)
    {
        $node = new Node();
        $node->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndLabelData
     * @param        string $name
     * @param        string $label
     * @param        string $expected
     */
    public function testWithNameAndLabel(string $name, string $label, string $expected)
    {
        $node = new Node();
        $node->withLabel($label)->named($name);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndPropertiesData
     * @param        string $name
     * @param        array  $properties
     * @param        string $expected
     */
    public function testWithNameAndProperties(string $name, array $properties, string $expected)
    {
        $node = new Node();
        $node->named($name)->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideWithLabelAndPropertiesData
     * @param        string $label
     * @param        array  $properties
     * @param        string $expected
     */
    public function testWithLabelAndProperties(string $label, array $properties, string $expected)
    {
        $node = new Node();
        $node->withLabel($label)->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndLabelAndPropertiesData
     * @param        string $name
     * @param        string $label
     * @param        array  $properties
     * @param        string $expected
     */
    public function testWithNameAndLabelAndProperties(string $name, string $label, array $properties, string $expected)
    {
        $node = new Node();
        $node->named($name)->withLabel($label)->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideBacktickThrowsExceptionData
     * @param        Node $invalidNode
     */
    public function testBacktickThrowsException(Node $invalidNode)
    {
        $this->expectException(\InvalidArgumentException::class);
        $invalidNode->toQuery();
    }

    /**
     * @dataProvider provideMultipleLabelsData
     * @param        array  $labels
     * @param        string $expected
     */
    public function testMultipleLabels(array $labels, string $expected)
    {
        $node = new Node();

        foreach ($labels as $label) {
            $node->withLabel($label);
        }

        $this->assertSame($expected, $node->toQuery());
    }

    public function testSetterSameAsConstructor()
    {
        $label = "__test__";
        $viaConstructor = new Node($label);
        $viaSetter = (new Node())->withLabel($label);

        $this->assertSame($viaConstructor->toQuery(), $viaSetter->toQuery(), "Setting label via setter has different effect than using constructor");
    }

    public function provideOnlyLabelData(): array
    {
        return [
        ['a', '(:a)'],
        ['A', '(:A)'],
        [':', '(:`:`)']
        ];
    }

    public function provideOnlyNameData(): array
    {
        return [
        ['a', '(a)'],
        ['A', '(A)'],
        [':', '(`:`)']
        ];
    }

    public function provideBacktickThrowsExceptionData(): array
    {
        return [
        [new Node('__`__')],
        [(new Node())->named('__`__')],
        [(new Node())->withProperties(['__`__' => new StringLiteral('a')])]
        ];
    }

    public function provideWithNameAndLabelData(): array
    {
        return [
        ['a', 'a', '(a:a)'],
        ['A', ':', '(A:`:`)'],
        ['', 'b', '(:b)']
        ];
    }

    public function provideWithNameAndPropertiesData()
    {
        return [
        ['a', ['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "(a {a: 'b', b: 'c'})"],
        ['b', ['a' => new Decimal(0), 'b' => new Decimal(1)], "(b {a: 0, b: 1})"],
        ['c', [':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "(c {`:`: [1, 'a']})"]
        ];
    }

    public function provideWithLabelAndPropertiesData()
    {
        return [
        ['a', ['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "(:a {a: 'b', b: 'c'})"],
        ['b', ['a' => new Decimal(0), 'b' => new Decimal(1)], "(:b {a: 0, b: 1})"],
        ['c', [':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "(:c {`:`: [1, 'a']})"]
        ];
    }

    public function provideOnlyPropertiesData()
    {
        return [
        [['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "({a: 'b', b: 'c'})"],
        [['a' => new Decimal(0), 'b' => new Decimal(1)], "({a: 0, b: 1})"],
        [[':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "({`:`: [1, 'a']})"]
        ];
    }

    public function provideWithNameAndLabelAndPropertiesData()
    {
        return [
        ['a', 'd', ['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "(a:d {a: 'b', b: 'c'})"],
        ['b', 'e', ['a' => new Decimal(0), 'b' => new Decimal(1)], "(b:e {a: 0, b: 1})"],
        ['c', 'f', [':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "(c:f {`:`: [1, 'a']})"]
        ];
    }

    public function provideMultipleLabelsData()
    {
        return [
        [['a'], '(:a)'],
        [['A'], '(:A)'],
        [[':'], '(:`:`)'],
        [['a', 'b'], '(:a:b)'],
        [['A', 'B'], '(:A:B)'],
        [[':', 'a'], '(:`:`:a)'],
        ];
    }
}