<?php

namespace Tests\Unit\Rules;

use App\Rules\CategoryHasGenreRule;
use Illuminate\Contracts\Validation\Rule;
use Mockery\MockInterface;
use Tests\TestCase;

class CategoryHasGenreRuleUnitTest extends TestCase
{

    public function testIfUseInterfaces()
    {
        $interfaces = [
            Rule::class
        ];

        $categoryHasGenreRuleInterfaces = array_keys(class_implements(CategoryHasGenreRule::class));
        $this->assertEquals($interfaces, $categoryHasGenreRuleInterfaces);
    }

    public function testGenresIdField()
    {
        $rule = new CategoryHasGenreRule([1, 1, 2, 2]);
        $reflectionClass = new \ReflectionClass(CategoryHasGenreRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genres');
        $reflectionProperty->setAccessible(true);

        $genres = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $genres);
    }

    public function testCategoriesIdField()
    {
        $rule = $this->createRuleMock([]);

        $rule
            ->shouldReceive('checkIfExistRelation')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1, 1, 2, 2]);

        $reflectionClass = new \ReflectionClass(CategoryHasGenreRule::class);
        $reflectionProperty = $reflectionClass->getProperty('categories');
        $reflectionProperty->setAccessible(true);

        $categories = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $categories);
    }

    public function testPassesReturnsFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    protected function createRuleMock(array $genres): MockInterface
    {
        return \Mockery::mock(CategoryHasGenreRule::class, [$genres])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}
