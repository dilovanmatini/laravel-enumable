<?php

namespace DilovanMatini\Enumable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EnumableServiceProvider extends PackageServiceProvider
{
    public function configurePackage (Package $package): void
    {
        $package
            ->name('laravel-enumable');
    }
}
