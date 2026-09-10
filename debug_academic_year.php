<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYear;
use App\Models\SystemSetting;

$connection = $app->make('db')->connection();
$schema = $connection->getSchemaBuilder();

if (!$schema->hasTable('academic_years')) {
    $schema->create('academic_years', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->date('start_date');
        $table->date('end_date');
        $table->boolean('is_current')->default(false);
        $table->string('status')->default('active');
        $table->timestamps();
    });
}
if (!$schema->hasTable('system_settings')) {
    $schema->create('system_settings', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->string('type')->default('text');
        $table->string('description')->nullable();
        $table->timestamps();
    });
}

AcademicYear::query()->delete();
SystemSetting::query()->delete();
AcademicYear::create([
    'name' => '2025/2026',
    'start_date' => '2025-09-01',
    'end_date' => '2026-07-31',
    'is_current' => true,
    'status' => 'active',
]);

$ay = AcademicYear::getCurrent();
echo "AcademicYear::getCurrent(): " . ($ay ? $ay->name : 'NULL') . "\n";
echo "AcademicYear::orderByDesc(start_date)->value('name'): " . AcademicYear::orderByDesc('start_date')->value('name') . "\n";
echo "SystemSetting::getValue('academic_year', fallback): " . SystemSetting::getValue('academic_year', AcademicYear::getCurrent()?->name ?? 'DEFAULT') . "\n";
