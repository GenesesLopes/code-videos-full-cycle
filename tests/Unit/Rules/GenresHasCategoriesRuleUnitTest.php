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
        $rule = new GenresHasCategoriesRule([]);
        $rule->passes('', [1, 1, 2, 2]);
        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $GenresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $GenresId);
    }
}