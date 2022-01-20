<?php
namespace spec\Ibexa\GraphQL\Tools\Stubs;

use Ibexa\GraphQL\Schema;

interface InitializableWorker extends Schema\Worker, Schema\Initializer
{

}
class_alias(InitializableWorker::class, 'spec\EzSystems\EzPlatformGraphQL\Tools\Stubs\InitializableWorker');
