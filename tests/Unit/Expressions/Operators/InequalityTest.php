<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality
 */
class InequalityTest extends TestCase
{
    public function testToQuery(): void
    {
        $inequality = new Inequality(new Property(new Variable('v'), "a"), new Property(new Variable('v'), "b"));

        $this->assertSame("(v.a <> v.b)", $inequality->toQuery());

        $inequality = new Inequality($inequality, $inequality);

        $this->assertSame("((v.a <> v.b) <> (v.a <> v.b))", $inequality->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $inequality = new Inequality(new Property(new Variable('v'), "a"), new Property(new Variable('v'), "b"), false);

        $this->assertSame("v.a <> v.b", $inequality->toQuery());

        $inequality = new Inequality($inequality, $inequality);

        $this->assertSame("(v.a <> v.b <> v.a <> v.b)", $inequality->toQuery());
    }
}
