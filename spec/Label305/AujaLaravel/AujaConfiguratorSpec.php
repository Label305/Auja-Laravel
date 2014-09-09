<?php

namespace spec\Label305\AujaLaravel;

use Illuminate\Foundation\Application;
use Label305\AujaLaravel\Model;
use Label305\AujaLaravel\Repositories\DatabaseRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AujaConfiguratorSpec extends ObjectBehavior {

    function let(Application $application, DatabaseRepository $databaseRepository) {
        $this->beConstructedWith($application, $databaseRepository);
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\AujaConfigurator');
    }

    function it_is_configurable() {
        $this->configure(array());
    }

    function it_should_haveModels() {
        $this->getModels()->shouldBeArray();
    }

    function it_should_have_relations() {
        $this->getRelations()->shouldBeArray();
    }

    function it_should_have_relations_for_models(Model $model) {
        $this->getRelationsForModel($model)->shouldBeArray();
    }
}
