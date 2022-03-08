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

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\HasPropertiesTrait;
use WikibaseSolutions\CypherDSL\Traits\NodeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Variable;
use function is_string;
use function trim;

/**
 * This class represents a node.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
 */
class Node implements NodeType
{
    use EscapeTrait;
    use NodeTypeTrait;
    use HasPropertiesTrait;

    /**
     * @var string[]
     */
    private array $labels = [];

    /**
     * @var Variable|null
     */
    private ?Variable $variable = null;

    /**
     * Node constructor.
     *
     * @param string|null $label
     */
    public function __construct(string $label = null)
    {
        if ($label !== null) {
            $this->labels[] = $label;
        }

        $this->properties = new PropertyMap();
    }

    /**
     * Returns the labels of the node.
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Returns the variable name of the node.
     *
     * @return Variable|null
     */
    public function getVariable(): ?Variable
    {
        return $this->variable;
    }

    /**
     * Names the node with a variable. If the variable is an empty string or null, it will be unset.
     *
     * @param Variable|string|null $variable
     * @return Node
     */
    public function named($variable): self
    {
        $this->assertClassOrType('variable', ['string', 'null', Variable::class], $variable);

        if (is_string($variable)) {
            if (trim($variable) === '') {
                $variable = null;
            } else {
                $variable = new Variable($variable);
            }
        }


        $this->variable = $variable;

        return $this;
    }

    /**
     * Returns the name of this node. This function automatically generates a name if the node does not have a
     * name yet.
     *
     * @return Variable The name of this node
     */
    public function getName(): Variable
    {
        if (!isset($this->variable)) {
            $this->named(new Variable());
        }

        return $this->variable;
    }

    /**
     * Alias of Node::named().
     *
     * @param $variable
     * @return $this
     * @see Node::named()
     */
    public function setName($variable): self
    {
        return $this->named($variable);
    }

    /**
     * @param string $label
     * @return Node
     */
    public function labeled(string $label): self
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     *
     * @return string
     */
    public function toQuery(): string
    {
        $nodeInner = "";

        if (isset($this->variable)) {
            $nodeInner .= $this->variable->toQuery();
        }

        if ($this->labels !== []) {
            foreach ($this->labels as $label) {
                $nodeInner .= ":{$this->escape($label)}";
            }
        }

        if (isset($this->properties)) {
            if ($nodeInner !== "") {
                $nodeInner .= " ";
            }

            $nodeInner .= $this->properties->toQuery();
        }

        return "($nodeInner)";
    }
}
