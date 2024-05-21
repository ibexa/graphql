<?php

namespace spec\Ibexa\GraphQL\DependencyInjection\GraphQL;

use Ibexa\Bundle\GraphQL\DependencyInjection\GraphQL\YamlSchemaProvider;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpSpec\ObjectBehavior;

/**
 * @TODO: Skipped, as out of the box phpspec have troubles solving YamlSchemaProviderSpec being in another namespace than rest of files.
 * Can be with replacing it with phpunit.
 * See: https://github.com/ibexa/http-cache/blob/main/spec/DependencyInjection/Compiler/KernelPassSpec.php#L11-L14
 */
class YamlSchemaProviderSpec extends ObjectBehavior
{
//     /**
//      * @vfsStreamDirectory
//      */
//     private $vfs;

//     function let()
//     {
//         $this->vfs = vfsStream::setUp('config');
//         $this->createFile('ezplatform/Platform.types.yaml');
//         $this->beConstructedWith($this->vfs->url());
//     }

//     function it_is_initializable()
//     {
//         $this->shouldHaveType(YamlSchemaProvider::class);
//     }

//     function it_returns_the_app_schema_if_one_exists()
//     {
//         $this->createFile('Query.types.yaml');
//         $this->getSchemaConfiguration()->shouldHaveQueryConfiguration('Query');
//     }

//     function it_returns_the_Domain_schema_if_no_app_schema_exists_and_the_domain_schema_exists()
//     {
//         $this->createFile('ezplatform/Domain.types.yaml');
//         $this->getSchemaConfiguration()->shouldHaveQueryConfiguration('Domain');
//     }

//     function it_returns_the_Platform_schema_if_no_app_and_domain_schema_exist()
//     {
//         $this->getSchemaConfiguration()->shouldHaveQueryConfiguration('Platform');
//     }

//     function it_returns_the_app_mutation_schema_if_one_exists()
//     {
//         $this->createFile('Mutation.types.yaml');
//         $this->getSchemaConfiguration()->shouldHaveMutationConfiguration('Mutation');
//     }

//     function it_returns_the_DomainMutation_schema_if_no_app_schema_exists_and_the_domain_mutation_schema_exists()
//     {
//         $this->createFile('ezplatform/DomainContentMutation.types.yaml');
//         $this->getSchemaConfiguration()->shouldHaveMutationConfiguration('DomainContentMutation');
//     }

//     function it_returns_no_mutation_schema_if_no_app_and_domain_mutation_schema_exist()
//     {
//         $this->getSchemaConfiguration()->shouldHaveMutationConfiguration(null);
//     }

//     public function getMatchers(): array
//     {
//         return [
//             'haveQueryConfiguration' => function($value, $expectedSchemaConfiguration) {
//                 return
//                     is_array($value) &&
//                     array_key_exists('query', $value) &&
//                     $value['query'] === $expectedSchemaConfiguration;
//             },
//             'haveMutationConfiguration' => function($value, $expectedMutationSchemaConfiguration) {
//                 return
//                     is_array($value) &&
//                     array_key_exists('mutation', $value) &&
//                     $value['mutation'] === $expectedMutationSchemaConfiguration;
//             }
//         ];
//     }

//     private function createFile($file)
//     {
//         $this->vfs->addChild(vfsStream::newFile($file));
//     }
}
