<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Personal Information
            $table->string('middle_name')->nullable()->after('last_name');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('date_of_birth');
            $table->string('blood_group')->nullable()->after('gender');
            $table->string('religion')->nullable()->after('blood_group');
            $table->string('nationality')->default('Nigerian')->after('religion');
            $table->string('state_of_origin')->nullable()->after('nationality');
            $table->string('local_government')->nullable()->after('state_of_origin');
            $table->text('home_address')->nullable()->after('local_government');
            $table->string('phone_number')->nullable()->after('home_address');
            $table->string('photo')->nullable()->after('email');
            
            // Parent Information
            $table->string('father_name')->nullable()->after('photo');
            $table->string('father_phone')->nullable()->after('father_name');
            $table->string('father_email')->nullable()->after('father_phone');
            $table->string('father_occupation')->nullable()->after('father_email');
            $table->string('mother_name')->nullable()->after('father_occupation');
            $table->string('mother_phone')->nullable()->after('mother_name');
            $table->string('mother_email')->nullable()->after('mother_phone');
            $table->string('mother_occupation')->nullable()->after('mother_email');
            $table->string('guardian_name')->nullable()->after('mother_occupation');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
            $table->string('guardian_email')->nullable()->after('guardian_phone');
            $table->text('guardian_address')->nullable()->after('guardian_email');
            $table->string('guardian_relationship')->nullable()->after('guardian_address');
            
            // Academic Information
            $table->string('roll_number')->nullable()->after('section');
            $table->string('house')->nullable()->after('roll_number');
            $table->string('previous_school')->nullable()->after('house');
            $table->date('admission_date')->nullable()->after('previous_school');
            $table->string('academic_year')->nullable()->after('admission_date');
            
            // Medical Information
            $table->text('allergies')->nullable()->after('blood_group');
            $table->text('medical_conditions')->nullable()->after('allergies');
            $table->string('emergency_contact_name')->nullable()->after('medical_conditions');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            
            // Fee Information
            $table->decimal('fee_concession', 10, 2)->default(0)->after('due_fees');
            
            // Transport Information
            $table->string('transport_route')->nullable()->after('fee_concession');
            $table->decimal('transport_fee', 10, 2)->default(0)->after('transport_route');
            
            // Hostel Information
            $table->boolean('is_hosteller')->default(false)->after('transport_fee');
            $table->string('hostel_name')->nullable()->after('is_hosteller');
            $table->string('room_number')->nullable()->after('hostel_name');
            
            // Status
            $table->boolean('is_alumni')->default(false)->after('is_active');
            $table->text('remarks')->nullable()->after('is_alumni');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name', 'gender', 'blood_group', 'religion', 'nationality',
                'state_of_origin', 'local_government', 'home_address', 'phone_number',
                'photo', 'father_name', 'father_phone', 'father_email', 'father_occupation',
                'mother_name', 'mother_phone', 'mother_email', 'mother_occupation',
                'guardian_name', 'guardian_phone', 'guardian_email', 'guardian_address',
                'guardian_relationship', 'roll_number', 'house', 'previous_school',
                'admission_date', 'academic_year', 'allergies', 'medical_conditions',
                'emergency_contact_name', 'emergency_contact_phone', 'fee_concession',
                'transport_route', 'transport_fee', 'is_hosteller', 'hostel_name',
                'room_number', 'is_alumni', 'remarks'
            ]);
        });
    }
};