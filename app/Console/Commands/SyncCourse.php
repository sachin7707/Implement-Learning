<?php
/**
 * @author Anders Hedegaard Nielsen <andersahn@gmail.com>
 * Date: 2/20/19
 * Time: 1:40 PM
 */

namespace App\Console\Commands;

use App\Jobs\ImportCourses;
use App\Maconomy\Client\Maconomy;
use Illuminate\Console\Command;

class SyncCourse extends Command
{
    protected $signature = "konform:importcourses";
    protected $description = "Import courses from Maconomy";

    public function handle()
    {
        $importer = new ImportCourses();
        $importer->handle(new Maconomy(env('MACONOMY_URL')));
    }
}
