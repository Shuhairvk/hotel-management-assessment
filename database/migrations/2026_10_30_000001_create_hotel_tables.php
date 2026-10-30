<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('staff');
            $t->json('permissions')->nullable();
            $t->boolean('is_active')->default(true);
        });
        Schema::create('guests', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('phone');
            $t->string('nationality');
            $t->string('photo')->nullable();
            $t->timestamps();
        });
        Schema::create('rooms', function (Blueprint $t) {
            $t->id();
            $t->string('room_number')->unique();
            $t->string('type');
            $t->decimal('price_per_night', 10, 2);
            $t->string('status')->default('available');
            $t->timestamps();
        });
        Schema::create('bookings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $t->foreignId('room_id')->constrained()->cascadeOnDelete();
            $t->date('check_in');
            $t->date('check_out');
            $t->string('status')->default('confirmed');
            $t->decimal('total_amount', 10, 2);
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('guests');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'permissions', 'is_active']));
    }
};
