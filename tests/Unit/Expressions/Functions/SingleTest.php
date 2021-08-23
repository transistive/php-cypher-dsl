<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\Single;

class SingleTest extends \PHPUnit\Framework\TestCase
{
    public function testToQuery() {
        $variable = FunctionTestHelper::getExpressionMock("variable", $this);
        $list = FunctionTestHelper::getExpressionMock("list", $this);
        $predicate = FunctionTestHelper::getExpressionMock("predicate", $this);

        $single = new Single($variable, $list, $predicate);

        $this->assertSame("single(variable IN list WHERE predicate)", $single->toQuery());
    }
}