<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('churches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name', 32);
            $table->string('slug')->unique();
            $table->string('timezone')->default('America/Fortaleza');
            $table->string('pix_key')->nullable();
            $table->string('logo_path')->nullable();
            $table->jsonb('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bulletins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('theme')->nullable();
            $table->string('status', 20);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['church_id', 'year', 'month']);
        });

        Schema::create('schedule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained()->cascadeOnDelete();
            $table->string('day_label');
            $table->string('description');
            $table->boolean('is_highlight')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('special_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained()->cascadeOnDelete();
            $table->date('event_date');
            $table->string('weekday_label', 16);
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained()->cascadeOnDelete();
            $table->date('service_date');
            $table->string('introducers')->nullable();
            $table->string('offertory')->nullable();
            $table->string('leaders')->nullable();
            $table->string('preachers')->nullable();
            $table->string('support')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('children_ministry_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained()->cascadeOnDelete();
            $table->date('service_date');
            $table->string('nursery')->nullable();
            $table->string('primary_class')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ebd_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained()->cascadeOnDelete();
            $table->string('class_name');
            $table->text('teachers_text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('birthdays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day');
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE bulletins ADD CONSTRAINT bulletins_month_range CHECK (month BETWEEN 1 AND 12)');
        DB::statement("ALTER TABLE bulletins ADD CONSTRAINT bulletins_status_check CHECK (status IN ('draft', 'published'))");
        DB::statement('ALTER TABLE birthdays ADD CONSTRAINT birthdays_day_range CHECK (day BETWEEN 1 AND 31)');
    }

    public function down(): void
    {
        Schema::dropIfExists('birthdays');
        Schema::dropIfExists('ebd_classes');
        Schema::dropIfExists('children_ministry_rosters');
        Schema::dropIfExists('service_rosters');
        Schema::dropIfExists('special_events');
        Schema::dropIfExists('schedule_items');
        Schema::dropIfExists('bulletins');
        Schema::dropIfExists('churches');
    }
};
