<?php

use DilovanMatini\Enumable\Traits\Enumable;

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

    public static function odds(): object
    {
        return self::generate([
            self::Case1,
            self::Case3
        ]);
    }
}

it('can count enum cases', function () {
    expect(SampleEnum::count())->toBe(3);
});

it('can get only specific cases', function () {
    $cases = SampleEnum::only(['case1', 'case2'])->cases();
    expect($cases)->toHaveCount(2);
    expect($cases[0]->name)->toBe('Case1');
    expect($cases[1]->name)->toBe('Case2');
});

it('can get all cases except specific ones', function () {
    $cases = SampleEnum::except(['case1'])->cases();
    expect($cases)->toHaveCount(2);
    expect($cases[1]->name)->toBe('Case2');
    expect($cases[2]->name)->toBe('Case3');
});

it('can generate cases from values', function () {
    $cases = SampleEnum::generate(['case1', 'case3'])->cases();
    expect($cases)->toHaveCount(2);
    expect($cases[0]->name)->toBe('Case1');
    expect($cases[1]->name)->toBe('Case3');
});

it('can get the label of an enum case', function () {
    $label = SampleEnum::Case3->label();
    expect($label)->toBe('Case Three');
});

it('can get the headline of an enum case', function () {
    $headline = SampleEnum::Case1->headline();
    expect($headline)->toBe('Case1');
});

it('can use Laravel Str helpers on enum case', function () {
    $slug = SampleEnum::Case1->str()->slug();
    expect($slug)->toBe('case1');
});

it('can get the default case', function () {
    $default = SampleEnum::default();
    expect($default->name)->toBe('Case2');
});

it('can get the default case label', function () {
    $label = SampleEnum::default()->label();
    expect($label)->toBe('Case2');
});

it('can generate cases from a method', function () {
    $cases = SampleEnum::odds()->cases();
    expect($cases)->toHaveCount(2);
    expect($cases[0]->name)->toBe('Case1');
    expect($cases[1]->name)->toBe('Case3');
});
