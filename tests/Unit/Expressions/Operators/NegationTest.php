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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Negation
 */
class NegationTest extends TestCase
{
    public function testToQuery()
    {
        $not = new Negation(new Boolean(true));

        $this->assertSame("(NOT true)", $not->toQuery());

        $not = new Negation($not);

        $this->assertSame("(NOT (NOT true))", $not->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands()
    {
        $this->expectException(TypeError::class);

        $and = new Negation($this->createMock(AnyType::class));

        $and->toQuery();
    }
}
