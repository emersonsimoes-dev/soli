<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('church_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['church_id', 'user_id']);
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedTinyInteger('birth_day')->nullable();
            $table->unsignedTinyInteger('birth_month')->nullable();
            $table->string('status', 20);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['church_id', 'status']);
        });

        Schema::create('roster_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ministry');
            $table->string('role');
            $table->date('service_date');
            $table->string('person_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['church_id', 'service_date']);
        });

        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->date('occurred_on');
            $table->string('type', 20);
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['church_id', 'occurred_on']);
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('status', 20);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['church_id', 'status']);
        });

        DB::statement('ALTER TABLE members ADD CONSTRAINT members_birth_day_range CHECK (birth_day IS NULL OR birth_day BETWEEN 1 AND 31)');
        DB::statement('ALTER TABLE members ADD CONSTRAINT members_birth_month_range CHECK (birth_month IS NULL OR birth_month BETWEEN 1 AND 12)');
        DB::statement("ALTER TABLE members ADD CONSTRAINT members_status_check CHECK (status IN ('active', 'inactive'))");
        DB::statement("ALTER TABLE contributions ADD CONSTRAINT contributions_type_check CHECK (type IN ('tithe', 'offering', 'other'))");
        DB::statement('ALTER TABLE contributions ADD CONSTRAINT contributions_amount_positive CHECK (amount >= 0)');
        DB::statement("ALTER TABLE announcements ADD CONSTRAINT announcements_status_check CHECK (status IN ('draft', 'published'))");

        $now = now();
        $churchIds = DB::table('churches')->pluck('id');
        $userIds = DB::table('users')->pluck('id');

        foreach ($userIds as $userId) {
            foreach ($churchIds as $churchId) {
                DB::table('church_user')->insert([
                    'church_id' => $churchId,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('contributions');
        Schema::dropIfExists('roster_entries');
        Schema::dropIfExists('members');
        Schema::dropIfExists('church_user');
    }
};
