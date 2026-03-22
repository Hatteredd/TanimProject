<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'location')) {
                $table->string('location', 200)->nullable()->after('name');
            }
            if (!Schema::hasColumn('employees', 'specialty')) {
                $table->string('specialty', 120)->nullable()->after('location');
            }
            if (!Schema::hasColumn('employees', 'contact_number')) {
                $table->string('contact_number', 30)->nullable()->after('specialty');
            }
        });

        DB::table('employees')->select(['id', 'name'])->orderBy('id')->chunk(200, function ($rows): void {
            foreach ($rows as $row) {
                $record = DB::table('employees')->where('id', $row->id)->first();
                $location = property_exists($record, 'location') ? $record->location : null;
                $specialty = property_exists($record, 'specialty') ? $record->specialty : null;

                if ((!$location || trim((string) $location) === '') && property_exists($record, 'department')) {
                    $location = $record->department;
                }

                if ((!$specialty || trim((string) $specialty) === '') && property_exists($record, 'position')) {
                    $specialty = $record->position;
                }

                DB::table('employees')->where('id', $row->id)->update([
                    'location' => $location ?: 'Unknown Location',
                    'specialty' => $specialty,
                ]);
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['position', 'department', 'base_salary', 'bonus', 'hire_date'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $dropColumns[] = $column;
                }
            }
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        DB::table('employees')->whereNull('location')->update(['location' => 'Unknown Location']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'position')) {
                $table->string('position')->nullable()->after('name');
            }
            if (!Schema::hasColumn('employees', 'department')) {
                $table->string('department')->nullable()->after('position');
            }
            if (!Schema::hasColumn('employees', 'base_salary')) {
                $table->decimal('base_salary', 10, 2)->default(0)->after('department');
            }
            if (!Schema::hasColumn('employees', 'bonus')) {
                $table->decimal('bonus', 10, 2)->default(0)->after('base_salary');
            }
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('bonus');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['location', 'specialty', 'contact_number'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $dropColumns[] = $column;
                }
            }
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
