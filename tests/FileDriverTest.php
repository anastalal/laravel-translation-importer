<?php

namespace Anastalal\TranslationImporter\Tests;

use Anastalal\LaravelTranslationImporter\TranslationImporterBindingsServiceProvider;
use Anastalal\LaravelTranslationImporter\TranslationImporterServiceProvider;
use Illuminate\Support\Facades\Event;
use Anastalal\LaravelTranslationImporter\Drivers\Translation;
use Anastalal\LaravelTranslationImporter\Exceptions\LanguageExistsException;
use Anastalal\TranslationImporter\Events\TranslationAdded;

use Orchestra\Testbench\TestCase;

class FileDriverTest extends TestCase
{
    private $translation;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        app()['path.lang'] = __DIR__.'/fixtures/lang';
        $this->translation = app()->make(Translation::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            TranslationImporterServiceProvider::class,
            TranslationImporterBindingsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('translation.driver', 'file');
    }

    /** @test */
    public function it_returns_all_languages()
    {
        $languages = $this->translation->allLanguages();

        $this->assertEquals($languages->count(), 2);
        $this->assertEquals($languages->toArray(), ['en' => 'en', 'es' => 'es']);
    }

    /** @test */
    public function it_returns_all_translations()
    {
        $translations = $this->translation->allTranslations();

        $this->assertEquals($translations->count(), 2);
        $this->assertEquals(['single' => ['single' => ['Hello' => 'Hello', "What's up" => "What's up!"]], 'group' => ['test' => ['hello' => 'Hello', 'whats_up' => "What's up!"]]], $translations->toArray()['en']);
        $this->assertArrayHasKey('en', $translations->toArray());
        $this->assertArrayHasKey('es', $translations->toArray());
    }

    /** @test */
    public function it_returns_all_translations_for_a_given_language()
    {
        $translations = $this->translation->allTranslationsFor('en');
        $this->assertEquals($translations->count(), 2);
        $this->assertEquals(['single' => ['single' => ['Hello' => 'Hello', "What's up" => "What's up!"]], 'group' => ['test' => ['hello' => 'Hello', 'whats_up' => "What's up!"]]], $translations->toArray());
        $this->assertArrayHasKey('single', $translations->toArray());
        $this->assertArrayHasKey('group', $translations->toArray());
    }

    /** @test */
    public function it_throws_an_exception_if_a_language_exists()
    {
        $this->expectException(LanguageExistsException::class);
        $this->translation->addLanguage('en');
    }

    /** @test */
    public function it_can_add_a_new_language()
    {
        $this->translation->addLanguage('fr');

        $this->assertTrue(file_exists(__DIR__.'/fixtures/lang/fr.json'));
        $this->assertTrue(file_exists(__DIR__.'/fixtures/lang/fr'));

        rmdir(__DIR__.'/fixtures/lang/fr');
        unlink(__DIR__.'/fixtures/lang/fr.json');
    }

    /** @test */
    public function it_can_add_a_new_translation_to_a_new_group()
    {
        $this->translation->addGroupTranslation('es', 'test', 'hello', 'Hola!');

        $translations = $this->translation->allTranslationsFor('es');

        $this->assertEquals(['test' => ['hello' => 'Hola!']], $translations->toArray()['group']);

        unlink(__DIR__.'/fixtures/lang/es/test.php');
    }

    /** @test */
    public function it_can_add_a_new_translation_to_an_existing_translation_group()
    {
        $this->translation->addGroupTranslation('en', 'test', 'test', 'Testing');

        $translations = $this->translation->allTranslationsFor('en');

        $this->assertEquals(['test' => ['hello' => 'Hello', 'whats_up' => 'What\'s up!', 'test' => 'Testing']], $translations->toArray()['group']);

        file_put_contents(
            app()['path.lang'].'/en/test.php',
            "<?php\n\nreturn ".var_export(['hello' => 'Hello', 'whats_up' => 'What\'s up!'], true).';'.\PHP_EOL
        );
    }

    /** @test */
    public function it_can_add_a_new_single_translation()
    {
        $this->translation->addSingleTranslation('es', 'single', 'Hello', 'Hola!');

        $translations = $this->translation->allTranslationsFor('es');

        $this->assertEquals(['single' => ['Hello' => 'Hola!']], $translations->toArray()['single']);

        unlink(__DIR__.'/fixtures/lang/es.json');
    }

    /** @test */
    public function it_can_add_a_new_single_translation_to_an_existing_language()
    {
        $this->translation->addSingleTranslation('en', 'single', 'Test', 'Testing');

        $translations = $this->translation->allTranslationsFor('en');

        $this->assertEquals(['single' => ['Hello' => 'Hello', 'What\'s up' => 'What\'s up!', 'Test' => 'Testing']], $translations->toArray()['single']);

        file_put_contents(
            app()['path.lang'].'/en.json',
            json_encode((object) ['Hello' => 'Hello', 'What\'s up' => 'What\'s up!'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }

    /** @test */
    public function it_can_get_a_collection_of_group_names_for_a_given_language()
    {
        $groups = $this->translation->getGroupsFor('en');

        $this->assertEquals($groups->toArray(), ['test']);
    }

    /** @test */
    public function it_can_merge_a_language_with_the_base_language()
    {
        $this->translation->addGroupTranslation('es', 'test', 'hello', 'Hola!');
        $translations = $this->translation->getSourceLanguageTranslationsWith('es');

        $this->assertEquals($translations->toArray(), [
            'group' => [
                'test' => [
                    'hello' => ['en' => 'Hello', 'es' => 'Hola!'],
                    'whats_up' => ['en' => "What's up!", 'es' => ''],
                ],
            ],
            'single' => [
                'single' => [
                    'Hello' => [
                        'en' => 'Hello',
                        'es' => '',
                    ],
                    "What's up" => [
                        'en' => "What's up!",
                        'es' => '',
                    ],
                ],
            ],
        ]);

        unlink(__DIR__.'/fixtures/lang/es/test.php');
    }

    /** @test */
    public function it_can_add_a_vendor_namespaced_translations()
    {
        $this->translation->addGroupTranslation('es', 'translation_test::test', 'hello', 'Hola!');

        $this->assertEquals($this->translation->allTranslationsFor('es')->toArray(), [
            'group' => [
                'translation_test::test' => [
                    'hello' => 'Hola!',
                ],
            ],
            'single' => [],
        ]);

        \File::deleteDirectory(__DIR__.'/fixtures/lang/vendor');
    }

    /** @test */
    public function it_can_add_a_nested_translation()
    {
        $this->translation->addGroupTranslation('en', 'test', 'test.nested', 'Nested!');

        $this->assertEquals($this->translation->getGroupTranslationsFor('en')->toArray(), [
            'test' => [
                'hello' => 'Hello',
                'test.nested' => 'Nested!',
                'whats_up' => 'What\'s up!',
            ],
        ]);

        file_put_contents(
            app()['path.lang'].'/en/test.php',
            "<?php\n\nreturn ".var_export(['hello' => 'Hello', 'whats_up' => 'What\'s up!'], true).';'.\PHP_EOL
        );
    }

    /** @test */
    public function it_can_add_nested_vendor_namespaced_translations()
    {
        $this->translation->addGroupTranslation('es', 'translation_test::test', 'nested.hello', 'Hola!');

        $this->assertEquals($this->translation->allTranslationsFor('es')->toArray(), [
            'group' => [
                'translation_test::test' => [
                    'nested.hello' => 'Hola!',
                ],
            ],
            'single' => [],
        ]);

        \File::deleteDirectory(__DIR__.'/fixtures/lang/vendor');
    }

    /** @test */
    public function it_can_merge_a_namespaced_language_with_the_base_language()
    {
        $this->translation->addGroupTranslation('en', 'translation_test::test', 'hello', 'Hello');
        $this->translation->addGroupTranslation('es', 'translation_test::test', 'hello', 'Hola!');
        $translations = $this->translation->getSourceLanguageTranslationsWith('es');

        $this->assertEquals($translations->toArray(), [
            'group' => [
                'test' => [
                    'hello' => ['en' => 'Hello', 'es' => ''],
                    'whats_up' => ['en' => "What's up!", 'es' => ''],
                ],
                'translation_test::test' => [
                    'hello' => ['en' => 'Hello', 'es' => 'Hola!'],
                ],
            ],
            'single' => [
                'single' => [
                    'Hello' => [
                        'en' => 'Hello',
                        'es' => '',
                    ],
                    "What's up" => [
                        'en' => "What's up!",
                        'es' => '',
                    ],
                ],
            ],
        ]);

        \File::deleteDirectory(__DIR__.'/fixtures/lang/vendor');
    }

    /** @test */
    public function a_list_of_languages_can_be_viewed()
    {
        $this->get(config('translation.ui_url'))
            ->assertSee('en');
    }
}