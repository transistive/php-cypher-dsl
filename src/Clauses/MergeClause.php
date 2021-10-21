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

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This class represents a MERGE clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
 */
class MergeClause extends Clause
{
    /**
     * @var StructuralType $pattern The pattern to merge
     */
    private StructuralType $pattern;

    /**
     * @var Clause $createClause The clause to execute when the pattern is created
     */
    private Clause $createClause;

    /**
     * @var Clause $matchClause The clause to execute when the pattern is matched
     */
    private Clause $matchClause;

    /**
     * Sets the pattern to merge.
     *
     * @param  StructuralType $pattern The pattern to merge
     * @return MergeClause
     */
    public function setPattern(StructuralType $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * The clause to execute on all nodes that need to be created.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/#merge-merge-with-on-create
     *
     * @param  Clause $createClause
     * @return MergeClause
     */
    public function setOnCreate(Clause $createClause): self
    {
        $this->createClause = $createClause;

        return $this;
    }

    /**
     * The clause to execute on all found nodes.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/#merge-merge-with-on-match
     *
     * @param  Clause $matchClause
     * @return MergeClause
     */
    public function setOnMatch(Clause $matchClause): self
    {
        $this->matchClause = $matchClause;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "MERGE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->pattern)) {
            return "";
        }

        $queryParts = [$this->pattern->toQuery()];

        if (isset($this->createClause)) {
            $queryParts[] = sprintf("ON CREATE %s", $this->createClause->toQuery());
        }

        if (isset($this->matchClause)) {
            $queryParts[] = sprintf("ON MATCH %s", $this->matchClause->toQuery());
        }

        return implode(" ", $queryParts);
    }
}