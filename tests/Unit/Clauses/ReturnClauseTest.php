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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\ReturnClause;
use WikibaseSolutions\CypherDSL\Expression;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\ReturnClause
 */
class ReturnClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $return = new ReturnClause();

        $this->assertSame("", $return->toQuery());
    }

    public function testSingleColumn()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"));

        $this->assertSame("RETURN a", $return->toQuery());
    }

    public function testMultipleColumns()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"));
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "b"));
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "c"));

        $this->assertSame("RETURN a, b, c", $return->toQuery());
    }

    public function testSingleAlias()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"), "b");

        $this->assertSame("RETURN a AS b", $return->toQuery());
    }

    public function testMultipleAliases()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"), "b");
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "b"), "c");

        $this->assertSame("RETURN a AS b, b AS c", $return->toQuery());
    }

    public function testMixedAliases()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"), "b");
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "c"));
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "b"), "c");

        $this->assertSame("RETURN a AS b, c, b AS c", $return->toQuery());
    }

    public function testAliasIsEscaped()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"), ":");

        $this->assertSame("RETURN a AS `:`", $return->toQuery());
    }

    public function testReturnDistinct()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"));
        $return->setDistinct();

        $this->assertSame("RETURN DISTINCT a", $return->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType()
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertableMock(AnyType::class, "a"));
        $return->setDistinct();

        $return->toQuery();
    }
}