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

use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;

/**
 * This class represents a DELETE clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
 */
class DeleteClause extends Clause
{
    /**
     * Whether the DETACH modifier is needed.
     *
     * @var bool $detach
     */
    private bool $detach = false;

    /**
     * The nodes that needs to be deleted.
     *
     * @var AnyType[] $nodes
     */
    private array $nodes = [];

    /**
     * Sets the clause to DETACH DELETE. Without DETACH DELETE, all relationships need to be explicitly
     * deleted.
     *
     * @param bool $detach Whether to use DETACH DELETE.
     * @return DeleteClause
     */
    public function setDetach(bool $detach = true): self
    {
        $this->detach = $detach;

        return $this;
    }

    /**
     * Add the node to be deleted. The expression must return a node when evaluated.
     *
     * @param NodeType $node Expression that returns the node to be deleted
     * @return DeleteClause
     */
    public function addNode(NodeType $node): self
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        if ($this->detach) {
            return "DETACH DELETE";
        }

        return "DELETE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(fn (NodeType $node) => $node->toQuery(), $this->nodes)
        );
    }
}