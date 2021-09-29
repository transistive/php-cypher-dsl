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
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MatchClause
 */
class MatchTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $match = new MatchClause();

        $this->assertSame("", $match->toQuery());
    }

    public function testSinglePattern()
    {
        $match = new MatchClause();
        $match->addPattern($this->getPatternMock("(a)", $this));

        $this->assertSame("MATCH (a)", $match->toQuery());
    }

    public function testMultiplePatterns()
    {
        $match = new MatchClause();
        $match->addPattern($this->getPatternMock("(a)", $this));
        $match->addPattern($this->getPatternMock("(b)", $this));

        $this->assertSame("MATCH (a), (b)", $match->toQuery());
    }
}