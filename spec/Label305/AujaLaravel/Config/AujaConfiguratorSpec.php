<?php

namespace spec\Label305\AujaLaravel\Config;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Label305\AujaLaravel\Config\Model;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Database\DatabaseHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AujaConfiguratorSpec extends ObjectBehavior {

    /**
     * @var Application
     */
    private $application;

    /**
     * @var DatabaseHelper
     */
    private $databaseHelper;

    function let(Application $application, DatabaseHelper $databaseHelper) {
        $this->application = $application;
        $this->databaseHelper = $databaseHelper;

        $this->beConstructedWith($application, $databaseHelper);

        Log::shouldReceive('debug');
    }

    function it_is_initializable() {
        $this->shouldHaveType('Label305\AujaLaravel\Config\AujaConfigurator');
    }

    function it_can_be_configured_with_models() {
        $this->configureClubConfigStubs();

        $this->configure(['Club']);
    }

    function it_can_return_models() {
        $this->configureClubConfigStubs();
        $this->configure(['Club']);

        $this->getModels()->shouldBeArray();
        $this->getModels()->shouldHaveCount(1);
    }

    function it_can_return_a_single_model() {
        $this->configureClubConfigStubs();
        $this->configure(['Club']);

        $this->getModel('Club')->shouldHaveType('Label305\AujaLaravel\Config\Model');
    }

    function it_can_return_relations() {
        $this->configureClubConfigStubs();
        $this->configure(['Club']);

        $this->getRelations()->shouldBeArray();
        $this->getRelations()->shouldHaveCount(1);
    }

    function it_can_return_relations_for_models(Model $model) {
        $this->configureClubConfigStubs();
        $this->configure(['Club']);

        $this->getRelationsForModel($model)->shouldBeArray();
    }

    function it_can_return_a_models_display_field(Model $model) {
        $this->configureClubConfigStubs();
        $this->configure(['Club']);
        $model->getName()->willReturn('Club');

        $this->getDisplayField($model)->shouldBe('id');
    }

    function it_throws_an_exception_when_requesting_a_display_field_for_an_unknown_model(Model $model){
        $this->configureClubConfigStubs();
        $this->configure(['Club']);
        $model->getName()->willReturn('House');

        $this->shouldThrow('\LogicException')->during('getDisplayField', [$model]);
    }

    function it_can_return_a_models_icon(Model $model) {
        $this->configureClubConfigStubs();
        $this->configure(['Club']);
        $model->getName()->willReturn('Club');

        $this->getIcon($model)->shouldBe('Icon');
    }

    function it_throws_an_exception_when_requesting_an_icon_for_an_unknown_model(Model $model){
        $this->configureClubConfigStubs();
        $this->configure(['Club']);
        $model->getName()->willReturn('House');

        $this->shouldThrow('\LogicException')->during('getIcon', [$model]);
    }

    function it_throws_exceptions_when_calling_methods_before_configure_is_called(Model $model) {
        $this->shouldThrow('\LogicException')->during('configure', [[]]);
        $this->shouldThrow('\LogicException')->during('getModels');
        $this->shouldThrow('\LogicException')->during('getModel', ['Club']);
        $this->shouldThrow('\LogicException')->during('getRelations');
        $this->shouldThrow('\LogicException')->during('getRelationsForModel', [$model]);
        $this->shouldThrow('\LogicException')->during('getDisplayField', [$model]);
        $this->shouldThrow('\LogicException')->during('getIcon', [$model]);
    }

    private function configureClubConfigStubs() {
        $config = new ModelConfig('Club');
        $config->setIcon('Icon');
        $this->application->make('ClubConfig', ['Club'])->willReturn($config);

        $this->databaseHelper->hasTable('clubs')->willReturn(true);
        $this->databaseHelper->getColumnListing('clubs')->willReturn(['id']);
        $this->databaseHelper->getColumnType('clubs', 'id')->willReturn('integer');
    }
}
