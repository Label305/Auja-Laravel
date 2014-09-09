<?php

namespace spec\Label305\AujaLaravel;

use Label305\AujaLaravel\Column;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModelSpec extends ObjectBehavior {

    function let() {
        $this->beConstructedWith('MyName');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Model');
    }

    function it_has_a_name() {
        $this->getName()->shouldBe('MyName');
    }

    function it_has_a_table_name() {
        $this->setTableName('MyTableName');
        $this->getTableName()->shouldBe('MyTableName');
    }

    function it_can_store_columns(Column $column1, Column $column2) {
        $column1->getName()->willReturn('Column 1');
        $column2->getName()->willReturn('Column 2');

        $this->addColumn($column1);
        $this->addColumn($column2);

        $this->getColumns()->shouldBeArray();
        $this->getColumns()->shouldContain($column1);
        $this->getColumns()->shouldContain($column2);
    }
}
