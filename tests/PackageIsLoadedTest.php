<?php

namespace Anastalal\TranslationImporter\Tests;

use Anastalal\LaravelTranslationImporter\TranslationImporterBindingsServiceProvider;
use Anastalal\LaravelTranslationImporter\TranslationImporterServiceProvider;
use Orchestra\Testbench\TestCase;

class PackageIsLoadedTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TranslationImporterServiceProvider::class,
            TranslationImporterBindingsServiceProvider::class,
        ];
    }

    /** @test */
    public function the_translation_pacakage_is_loaded()
    {
        $this->assertArrayHasKey(TranslationImporterServiceProvider::class, app()->getLoadedProviders());
        $this->assertArrayHasKey(TranslationImporterBindingsServiceProvider::class, app()->getLoadedProviders());
    }
}