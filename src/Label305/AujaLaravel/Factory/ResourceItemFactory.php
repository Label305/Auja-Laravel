<?php
/**
 * Created by PhpStorm.
 * User: Thijs
 * Date: 16-12-14
 * Time: 16:07
 */

namespace Label305\AujaLaravel\Factory;


use Illuminate\Support\Facades\URL;
use Label305\Auja\Menu\Property\Searchable;
use Label305\Auja\Menu\ResourceMenuItem;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Config\ModelConfig;
use Label305\AujaLaravel\Routing\AujaRouter;

class ResourceItemFactory {

    /**
     * @var AujaConfigurator
     */
    private $aujaConfigurator;

    /**
     * @var AujaRouter
     */
    private $aujaRouter;

    public function __construct(AujaConfigurator $aujaConfigurator, AujaRouter $aujaRouter) {
        $this->aujaConfigurator = $aujaConfigurator;
        $this->aujaRouter = $aujaRouter;
    }

    /**
     * Builds a simple menu for given model, where typically this model should not have any relations to other models.
     *
     * The menu will include:
     *  - An Add LinkMenuItem;
     *  - A SpacerMenuItem with the model's name;
     *  - A ResourceMenuItem to hold entries of the model.
     *
     * @param String      $modelName The name of the model.
     * @param ModelConfig $config    (optional) The `ModelConfig` to use.
     *
     * @return ResourceMenuItem the Menu, which can be configured further.
     */
    public function create($modelName, ModelConfig $config = null) {

        $resourceMenuItem = new ResourceMenuItem();
        $resourceMenuItem->setTarget(URL::route($this->aujaRouter->getIndexName($modelName)));

        $model = $this->aujaConfigurator->getModel($modelName);
        if($this->aujaConfigurator->isSearchable($model, $config)){
            $target = urldecode(URL::route($this->aujaRouter->getIndexName($modelName), ['q' => '%s'])); /* urldecode because the '%' gets escaped. */
            $property = new Searchable($target);
            $resourceMenuItem->addProperty($property);
        }

        return $resourceMenuItem;
    }
}