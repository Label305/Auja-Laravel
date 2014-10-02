<?php

namespace spec\Label305\AujaLaravel\Config;

use Label305\Auja\Icons;
use Label305\AujaLaravel\Config\Column;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModelConfigSpec extends ObjectBehavior {

    function let() {
        $this->beConstructedWith('Club');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Config\ModelConfig');
    }

    function it_deduces_a_table_name() {
        $this->getTableName()->shouldBe('clubs');
    }

    function it_can_overwrite_the_table_name() {
        $this->setTableName('TableName');
        $this->getTableName()->shouldBe('TableName');
    }

    function it_has_a_display_field() {
        $this->setDisplayField('Field');
        $this->getDisplayField()->shouldBe('Field');
    }

    function it_has_an_icon() {
        $this->setIcon(Icons::alert);
        $this->getIcon()->shouldBe(Icons::alert);
    }
}
