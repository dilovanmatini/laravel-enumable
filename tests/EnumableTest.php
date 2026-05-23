<?php

use DilovanMatini\Enumable\Contracts\EnumSubsetContract;
use DilovanMatini\Enumable\Traits\Enumable;
use Illuminate\Validation\Rules\Enum as EnumRule;

enum SampleEnum: string
{
    use Enumable;

    case Case1 = 'case1';
    case Case2 = 'case2';
    case Case3 = 'case3';

    public static function default(): self
    {
        return self::Case2;
    }

    public static function setLabels(): array
    {
        return [
            self::Case3->value => 'Case Three',
        ];
    }

    public static function odds(): EnumSubsetContract
    {
        return self::generate([
            self::Case1,
            self::Case3,
        ]);
    }
}

enum IntSampleEnum: int
{
    use Enumable;

    case One = 1;
    case Two = 2;
}

enum PlainEnum
{
    use Enumable;

    case Alpha;
    case Beta;
}

it('can count enum cases', function () {
    expect(SampleEnum::count())->toBe(3);
});

it('returns all values and names', function () {
    expect(SampleEnum::values())->toBe(['case1', 'case2', 'case3']);
    expect(SampleEnum::names())->toBe(['Case1', 'Case2', 'Case3']);
});

it('can convert to array and collection', function () {
    expect(SampleEnum::toArray())->toBe([
        'case1' => 'Case1',
        'case2' => 'Case2',
        'case3' => 'Case3',
    ]);

    expect(SampleEnum::toCollection())->toHaveCount(3);
});

it('can get only specific cases by value', function () {
    $subset = SampleEnum::only(['case1', 'case2']);

    expect($subset)->toBeObject();
    expect($subset->cases())->toHaveCount(2);
    expect($subset->cases()[0]->name)->toBe('Case1');
    expect($subset->cases()[1]->name)->toBe('Case2');
});

it('can call static methods on a subset for backward compatibility', function () {
    expect(SampleEnum::only(['case1', 'case3'])::labels())->toBe([
        'case1' => 'Case1',
        'case3' => 'Case Three',
    ]);

    expect(SampleEnum::only(['case1', 'case3'])::values())->toBe(['case1', 'case3']);
});

it('can get only specific cases by enum instance', function () {
    $subset = SampleEnum::only([SampleEnum::Case1, SampleEnum::Case3]);

    expect($subset->values())->toBe(['case1', 'case3']);
});

it('can get all cases except specific ones with zero-based keys', function () {
    $cases = SampleEnum::except(['case1'])->cases();

    expect($cases)->toHaveCount(2);
    expect(array_is_list($cases))->toBeTrue();
    expect($cases[0]->name)->toBe('Case2');
    expect($cases[1]->name)->toBe('Case3');
});

it('can generate cases from values', function () {
    $cases = SampleEnum::generate(['case1', 'case3'])->cases();

    expect($cases)->toHaveCount(2);
    expect($cases[0]->name)->toBe('Case1');
    expect($cases[1]->name)->toBe('Case3');
});

it('keeps independent subsets', function () {
    $first = SampleEnum::only(['case1']);
    $second = SampleEnum::only(['case2', 'case3']);

    expect($first->cases())->toHaveCount(1);
    expect($second->cases())->toHaveCount(2);
    expect($first->cases()[0]->name)->toBe('Case1');
    expect($second->cases()[0]->name)->toBe('Case2');
    expect($first::labels())->toBe(['case1' => 'Case1']);
    expect($second::labels())->toBe([
        'case2' => 'Case2',
        'case3' => 'Case Three',
    ]);
});

it('can get the label of an enum case', function () {
    expect(SampleEnum::Case3->label())->toBe('Case Three');
});

it('can get label by value without custom label', function () {
    expect(SampleEnum::getLabel('case1'))->toBe('Case1');
});

it('can get the headline of an enum case', function () {
    expect(SampleEnum::Case1->headline())->toBe('Case1');
});

it('can use string helpers on enum case', function () {
    expect(SampleEnum::Case1->str()->slug())->toBe('case1');
    expect(SampleEnum::Case1->str(true)->headline())->toBe('Case1');
});

it('can resolve cases strictly', function () {
    expect(SampleEnum::getCase('case1'))->toBe(SampleEnum::Case1);
    expect(SampleEnum::getCase('1'))->toBeNull();
    expect(SampleEnum::tryFromValue('case2'))->toBe(SampleEnum::Case2);
});

it('can resolve cases by name', function () {
    expect(SampleEnum::fromName('Case3'))->toBe(SampleEnum::Case3);
    expect(SampleEnum::fromNameOrDefault('Missing'))->toBe(SampleEnum::Case2);
});

it('can check existence', function () {
    expect(SampleEnum::exists('case1'))->toBeTrue();
    expect(SampleEnum::exists(SampleEnum::Case2))->toBeTrue();
    expect(SampleEnum::exists('invalid'))->toBeFalse();
});

it('can compare enum instances', function () {
    expect(SampleEnum::Case1->is(SampleEnum::Case1))->toBeTrue();
    expect(SampleEnum::Case1->is('case1'))->toBeTrue();
    expect(SampleEnum::Case1->isAny(['case2', 'case1']))->toBeTrue();
    expect(SampleEnum::Case1->in(['case2', 'case3']))->toBeFalse();
});

it('can get the default case when overridden', function () {
    expect(SampleEnum::default())->toBe(SampleEnum::Case2);
});

it('can get the default case label', function () {
    expect(SampleEnum::default()->label())->toBe('Case2');
});

it('can get first and last cases', function () {
    expect(SampleEnum::first())->toBe(SampleEnum::Case1);
    expect(SampleEnum::last())->toBe(SampleEnum::Case3);
});

it('can generate cases from a method', function () {
    $cases = SampleEnum::odds()->cases();

    expect($cases)->toHaveCount(2);
    expect($cases[0]->name)->toBe('Case1');
    expect($cases[1]->name)->toBe('Case3');
});

it('can get the array for html select tag', function () {
    $select = SampleEnum::toSelectArray();

    expect($select)->toBeArray();
    expect($select)->toHaveCount(3);
    expect($select['case3'])->toBe('Case Three');
});

it('can get select array by name', function () {
    expect(SampleEnum::toSelectArrayByName())->toBe([
        'Case1' => 'Case1',
        'Case2' => 'Case2',
        'Case3' => 'Case Three',
    ]);
});

it('works with int backed enums', function () {
    expect(IntSampleEnum::values())->toBe([1, 2]);
    expect(IntSampleEnum::getCase(2))->toBe(IntSampleEnum::Two);
    expect(IntSampleEnum::exists(1))->toBeTrue();
});

it('throws for unit enums when values are requested', function () {
    PlainEnum::values();
})->throws(InvalidArgumentException::class);

it('can still resolve unit enum cases by name', function () {
    expect(PlainEnum::fromName('Alpha'))->toBe(PlainEnum::Alpha);
    expect(PlainEnum::exists('Alpha'))->toBeTrue();
});

it('provides validation rules', function () {
    expect(SampleEnum::rule())->toBeInstanceOf(EnumRule::class);
    expect(SampleEnum::ruleOnly(['case1'])->__toString())->toContain('case1');
    expect(SampleEnum::ruleExcept(['case1'])->__toString())->toContain('case2');
});

it('translates using a conventional key', function () {
    expect(SampleEnum::Case1->trans('custom.key'))->toBe('custom.key');
});
