<?php

namespace spec\Label305\AujaLaravel\Config;

use Label305\Auja\Icons;
use Label305\AujaLaravel\Config\Column;
use Label305\AujaLaravel\Config\Model;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RelationSpec extends ObjectBehavior {

    /**
     * @var Model
     */
    private $leftModel;

    /**
     * @var Model
     */
    private $rightModel;

    function let(Model $left, Model $right) {
        $this->beConstructedWith($left, $right, 'belongs_to');

        $this->leftModel = $left;
        $this->rightModel = $right;
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Config\Relation');
    }

    function it_can_return_the_models() {
        $this->getLeft()->shouldBe($this->leftModel);
        $this->getRight()->shouldBe($this->rightModel);
    }

    function it_can_return_the_type() {
        $this->getType()->shouldBe('belongs_to');
    }
}
