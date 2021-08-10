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

use WikibaseSolutions\CypherDSL\EscapeTrait;
use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * This class represents a RETURN clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
 */
class ReturnClause extends Clause
{
	use EscapeTrait;

	/**
	 * @var bool Whether to be a RETURN DISTINCT query
	 */
	private bool $distinct = false;

	/**
	 * @var array The expressions to return
	 */
	private array $expressions = [];

	/**
	 * Sets this query to only retrieve unique rows.
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-unique-results
	 * @param bool $distinct
	 * @return ReturnClause
	 */
	public function setDistinct(bool $distinct = true): self
	{
		$this->distinct = $distinct;

		return $this;
	}

	/**
	 * Add a new column to this RETURN clause.
	 *
	 * @param Expression $expression The expression to return
	 * @param string $alias The alias of this column
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
	 * @return ReturnClause
	 */
	public function addColumn(Expression $expression, string $alias = ""): self
	{
		if ($alias !== "") {
			$this->expressions[$alias] = $expression;
		} else {
			$this->expressions[] = $expression;
		}

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function getClause(): string
	{
		return $this->distinct ?
			"RETURN DISTINCT" :
			"RETURN";
	}

	/**
	 * @inheritDoc
	 */
	protected function getSubject(): string
	{
		$expressions = [];

		foreach ($this->expressions as $alias => $expression) {
			$expressionQuery = $expression->toQuery();
			$expressions[] = is_int($alias) ?
				$expressionQuery :
				sprintf("%s AS %s", $expressionQuery,  $this->escape($alias));
		}

		return implode(", ", $expressions);
	}
}