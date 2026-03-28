<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('street')->nullable();
            $table->string('suite')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('name')->nullable();
            $table->string('catch_phrase')->nullable();
            $table->string('bs')->nullable();
            $table->timestamps();
        });

        if (Schema::hasColumn('users', 'address') || Schema::hasColumn('users', 'company')) {
            $users = DB::table('users')->select('id', 'address', 'company')->get();

            foreach ($users as $user) {
                $address = is_string($user->address) ? json_decode($user->address, true) : $user->address;
                $company = is_string($user->company) ? json_decode($user->company, true) : $user->company;

                if (is_array($address)) {
                    DB::table('addresses')->updateOrInsert(
                        ['user_id' => $user->id],
                        [
                            'street' => $address['street'] ?? null,
                            'suite' => $address['suite'] ?? null,
                            'city' => $address['city'] ?? null,
                            'zipcode' => $address['zipcode'] ?? null,
                            'lat' => $address['geo']['lat'] ?? null,
                            'lng' => $address['geo']['lng'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                if (is_array($company)) {
                    DB::table('companies')->updateOrInsert(
                        ['user_id' => $user->id],
                        [
                            'name' => $company['name'] ?? null,
                            'catch_phrase' => $company['catchPhrase'] ?? null,
                            'bs' => $company['bs'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['address', 'company']);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'address') && !Schema::hasColumn('users', 'company')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('address')->nullable()->after('email');
                $table->json('company')->nullable()->after('address');
            });

            $users = DB::table('users')->select('id')->get();

            foreach ($users as $user) {
                $address = DB::table('addresses')->where('user_id', $user->id)->first();
                $company = DB::table('companies')->where('user_id', $user->id)->first();

                DB::table('users')->where('id', $user->id)->update([
                    'address' => $address ? json_encode([
                        'street' => $address->street,
                        'suite' => $address->suite,
                        'city' => $address->city,
                        'zipcode' => $address->zipcode,
                        'geo' => [
                            'lat' => $address->lat,
                            'lng' => $address->lng,
                        ],
                    ]) : null,
                    'company' => $company ? json_encode([
                        'name' => $company->name,
                        'catchPhrase' => $company->catch_phrase,
                        'bs' => $company->bs,
                    ]) : null,
                ]);
            }
        }

        Schema::dropIfExists('companies');
        Schema::dropIfExists('addresses');
    }
};
