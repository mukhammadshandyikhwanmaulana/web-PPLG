<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memastikan kompatibilitas antara kolom `created_by` dan `user_id`
     * tanpa merusak struktur database yang sudah berjalan.
     */
    public function up(): void
    {
        // 1. Student Works
        if (Schema::hasTable('student_works')) {
            if (! Schema::hasColumn('student_works', 'user_id')) {
                Schema::table('student_works', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                });
                
                // Sinkronkan nilai user_id dari created_by jika ada
                if (Schema::hasColumn('student_works', 'created_by')) {
                    DB::statement('UPDATE student_works SET user_id = created_by WHERE user_id IS NULL AND created_by IS NOT NULL');
                }
            }
        }

        // 2. Activities
        if (Schema::hasTable('activities')) {
            if (! Schema::hasColumn('activities', 'user_id')) {
                Schema::table('activities', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                });

                if (Schema::hasColumn('activities', 'created_by')) {
                    DB::statement('UPDATE activities SET user_id = created_by WHERE user_id IS NULL AND created_by IS NOT NULL');
                }
            }
        }

        // 3. Achievements
        if (Schema::hasTable('achievements')) {
            if (! Schema::hasColumn('achievements', 'user_id')) {
                Schema::table('achievements', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                });

                if (Schema::hasColumn('achievements', 'created_by')) {
                    DB::statement('UPDATE achievements SET user_id = created_by WHERE user_id IS NULL AND created_by IS NOT NULL');
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_works') && Schema::hasColumn('student_works', 'user_id')) {
            Schema::table('student_works', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('activities') && Schema::hasColumn('activities', 'user_id')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('achievements') && Schema::hasColumn('achievements', 'user_id')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};