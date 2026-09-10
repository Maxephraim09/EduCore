<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('group')->default('general'); // application, recruitment, registration
            $table->string('description')->nullable();
            $table->boolean('is_editable')->default(true);
            $table->timestamps();

            $table->index('group');
        });

        // Seed default settings
        $defaults = [
            ['key' => 'application_status', 'value' => 'open', 'type' => 'string', 'group' => 'application', 'description' => 'Application status (open/closed)'],
            ['key' => 'application_fee', 'value' => '5000', 'type' => 'integer', 'group' => 'application', 'description' => 'Application fee amount'],
            ['key' => 'application_start_date', 'value' => now()->toDateString(), 'type' => 'string', 'group' => 'application', 'description' => 'Application start date'],
            ['key' => 'application_end_date', 'value' => now()->addMonths(3)->toDateString(), 'type' => 'string', 'group' => 'application', 'description' => 'Application end date'],
            ['key' => 'required_documents', 'value' => json_encode(['birth_certificate', 'passport_photo', 'report_card']), 'type' => 'json', 'group' => 'application', 'description' => 'Required documents for application'],

            // Recruitment settings
            ['key' => 'recruitment_status', 'value' => 'open', 'type' => 'string', 'group' => 'recruitment', 'description' => 'Recruitment status (open/closed)'],
            ['key' => 'default_application_deadline_days', 'value' => '30', 'type' => 'integer', 'group' => 'recruitment', 'description' => 'Default application deadline in days'],
            ['key' => 'recruitment_required_documents', 'value' => json_encode(['cv', 'cover_letter', 'certificates']), 'type' => 'json', 'group' => 'recruitment', 'description' => 'Required documents for recruitment'],
            ['key' => 'auto_create_user_on_acceptance', 'value' => 'true', 'type' => 'boolean', 'group' => 'recruitment', 'description' => 'Auto create user account when accepted'],
            ['key' => 'default_employee_role', 'value' => 'teacher', 'type' => 'string', 'group' => 'recruitment', 'description' => 'Default role for hired employees'],
        ];

        foreach ($defaults as $setting) {
            $setting['is_editable'] = true;
            \DB::table('application_settings')->insert($setting);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('application_settings');
    }
};

