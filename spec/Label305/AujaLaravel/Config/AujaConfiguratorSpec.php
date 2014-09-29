<?php

namespace spec\Label305\AujaLaravel\Config;

use Illuminate\Foundation\Application;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Logging\Logger;
use Label305\AujaLaravel\Database\DatabaseHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AujaConfiguratorSpec extends ObjectBehavior {

    function let(Application $application, DatabaseHelper $databaseRepository, Logger $logger) {
        $this->beConstructedWith($application, $databaseRepository, $logger);
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Config\AujaConfigurator');
    }

    function it_is_configurable() {
        $this->configure([]);
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
