<?php

namespace spec\Label305\AujaLaravel;

use Illuminate\Foundation\Application;
use Label305\AujaLaravel\Logging\Logger;
use Label305\AujaLaravel\Model;
use Label305\AujaLaravel\Database\DatabaseHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AujaConfiguratorSpec extends ObjectBehavior {

    function let(Application $application, DatabaseHelper $databaseRepository, Logger $logger) {
        $this->beConstructedWith($application, $databaseRepository, $logger);
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
