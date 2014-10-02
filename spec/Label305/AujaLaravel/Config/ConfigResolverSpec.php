<?php

namespace spec\Label305\AujaLaravel\Config;

use Doctrine\DBAL\Types\Type;
use Exception;
use Illuminate\Foundation\Application;
use Label305\AujaLaravel\Config\Column;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Config\Model;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigResolverSpec extends ObjectBehavior {

    function let(Application $application, Model $model) {
        $this->beConstructedWith($application, $model);

        $application->make('NameConfig', ['Name'])->willReturn(new ModelConfig('Name'));

        $model->getName()->willReturn('Name');
        $model->getColumns()->willReturn([
            new Column('id', Type::INTEGER)
        ]);
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Config\ConfigResolver');
    }

    function it_can_resolve_a_config() {
        $this->resolve()->shouldHaveType('Label305\AujaLaravel\Config\ModelConfig');
    }

    function it_can_guess_a_name_displayfield(Application $application, Model $model) {
        $this->beConstructedWith($application, $model);

        $application->make('NameConfig', ['Name'])->willThrow('ReflectionException');

        $model->getName()->willReturn('Name');
        $model->getColumns()->willReturn([
            new Column('id', Type::INTEGER),
            new Column('name', Type::STRING)
        ]);

        $this->resolve()->shouldHaveType('Label305\AujaLaravel\Config\ModelConfig');
    }

    function it_creates_a_new_config_if_none_found(Application $application, Model $model) {
        $this->beConstructedWith($application, $model);

        $application->make('NameConfig', ['Name'])->willReturn(new ModelConfig('Name'));

        $model->getName()->willReturn('Name');
        $model->getColumns()->willReturn([
            new Column('id', Type::INTEGER)
        ]);
    }

}
