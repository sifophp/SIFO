<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

include_once 'constants.php';

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);

    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_74);
    $parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, getcwd() . '/phpstan.neon');

    // get services (needed for register a single rule)
     $services = $containerConfigurator->services();

    // register a single rule
     $services->set(TypedPropertyRector::class);
     $services->set(ClosureToArrowFunctionRector::class);
     $services->set(AddDefaultValueForUndefinedVariableRector::class);
};
