<?php

namespace spec\Label305\AujaLaravel;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ColumnSpec extends ObjectBehavior {

    function let() {
        $this->beConstructedWith('MyName', 'MyType');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Column');
    }

    function it_should_have_a_name() {
        $this->getName()->shouldBe('MyName');
    }

    function it_should_have_a_type() {
        $this->getType()->shouldBe('MyType');
    }


}
