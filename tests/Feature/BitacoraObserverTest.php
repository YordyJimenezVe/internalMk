<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BitacoraObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_with_name_changes_is_logged_in_bitacora(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name'
        ]);

        $this->actingAs($user);

        $user->update([
            'name' => 'New Name'
        ]);

        $this->assertDatabaseHas('bitacoras', [
            'users_id' => $user->id,
        ]);

        $log = Bitacora::where('users_id', $user->id)->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('ACTUALIZACIÓN DE USUARIO', $log->action);
        $this->assertStringContainsString("SE CAMBIÓ 'NAME' DE 'ORIGINAL NAME' A 'NEW NAME'", $log->description);
    }

    public function test_user_update_with_only_remember_token_changes_is_ignored(): void
    {
        $user = User::factory()->create([
            'remember_token' => 'original-token'
        ]);

        $this->actingAs($user);

        $user->update([
            'remember_token' => 'new-token'
        ]);

        $this->assertDatabaseCount('bitacoras', 0);
    }

    public function test_user_update_with_name_and_remember_token_changes_logs_only_name(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'remember_token' => 'original-token'
        ]);

        $this->actingAs($user);

        $user->update([
            'name' => 'New Name',
            'remember_token' => 'new-token'
        ]);

        $this->assertDatabaseCount('bitacoras', 1);

        $log = Bitacora::first();
        $this->assertStringContainsString('ACTUALIZACIÓN DE USUARIO', $log->action);
        $this->assertStringContainsString("SE CAMBIÓ 'NAME' DE 'ORIGINAL NAME' A 'NEW NAME'", $log->description);
        $this->assertStringNotContainsString("remember_token", strtolower($log->description));
    }
}
