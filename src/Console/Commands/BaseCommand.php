<?php
namespace Anastalal\LaravelTranslationImporter\Console\Commands;

use Illuminate\Console\Command;
use Anastalal\LaravelTranslationImporter\Drivers\Translation;

class BaseCommand extends Command
{
    protected $translation;

    public function __construct(Translation $translation)
    {
        parent::__construct();
        $this->translation = $translation;
    }
}