<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use Mockery\MockInterface;
use Tests\TestCase;
use App\Rules\GenresHasCategoriesRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class GenresHasCategoriesRuleUnitTest extends TestCase
{


    public function testInstanceRule()
    {
        $rule = new GenresHasCategoriesRule([1]);
        $this->assertInstanceOf(Rule::class, $rule);
    }

    public function testCategoriesIdField()
    {
        $rule = new GenresHasCategoriesRule(
            [1, 1, 2, 2]
        );
        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);

        $categoryId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $categoryId);
    }

    public function testGenresIdValue()
    {

        $rule = $this->createRuleMock([]);

        $rule->passes('', [1, 1, 2, 2]);
        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $GenresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $GenresId);
    }

    public function testPassesReturnsFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenGetRowsIsEmpty()
    {
        $rule = $this->createRuleMock([1]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenHasCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1, 2]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesIsValid()
    {
        $rule = $this->createRuleMock([1, 2]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));
        $this->assertTrue($rule->passes('', [1]));

        $rule = $this->createRuleMock([1, 2]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2]
            ]));
        $this->assertTrue($rule->passes('', [1]));
    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return \Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}