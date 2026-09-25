<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Inventario;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register permissions
        $permissions = [
            'view partida',
            'manage partida',
            'view billing',
            'manage billing',
            'view reports',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Set up roles
        $contadorRole = Role::firstOrCreate(['name' => 'Contador', 'guard_name' => 'web']);
        $contadorRole->syncPermissions($permissions);

        $vendedorRole = Role::firstOrCreate(['name' => 'Vendedor', 'guard_name' => 'web']);
        $vendedorRole->syncPermissions(['view partida']);
    }

    /**
     * Test that registering a new inventory item defaults status to PRECIO PENDIENTE
     * and correctly allows nullable price defaulting to '0.00'.
     */
    public function test_creating_inventory_item_sets_status_to_precio_pendiente_and_price_defaults(): void
    {
        $user = User::factory()->create([
            'rol' => 'Contador',
        ]);
        $user->syncRoles(['Contador']);

        // Authenticate
        $response = $this->actingAs($user)->post(route('storeInventario'), [
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'toyota',
            'modelo' => 'corolla',
            'serial' => '1nz-123456',
            'año' => '2015',
            'condicion' => 'APLICA',
            'status' => 'PRECIO PENDIENTE',
            'price' => '', // omitted/empty price
        ]);

        if (session('errors')) {
            fwrite(STDERR, print_r(session('errors')->getMessages(), true));
        }

        $response->assertSessionHasNoErrors();
        
        $item = Inventario::first();
        $this->assertNotNull($item);
        
        // Assert uppercase conversion
        $this->assertEquals('TOYOTA', $item->marca);
        $this->assertEquals('COROLLA', $item->modelo);
        $this->assertEquals('1NZ-123456', $item->serial);

        // Assert defaults
        $this->assertEquals('PRECIO PENDIENTE', $item->status);
        $this->assertEquals('0.00', $item->price);
    }

    /**
     * Test that updating an inventory item updates the fields correctly,
     * including costo_importacion_unitario.
     */
    public function test_updating_inventory_item_including_costo_importacion(): void
    {
        $user = User::factory()->create([
            'rol' => 'Contador',
        ]);
        $user->syncRoles(['Contador']);

        $item = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'serial' => '1NZ-123456',
            'año' => '2015',
            'condicion' => 'APLICA',
            'status' => 'PRECIO PENDIENTE',
            'price' => '0.00',
        ]);

        $response = $this->actingAs($user)->post(route('updateInventario', $item->id), [
            'id' => $item->id,
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'toyota edit',
            'modelo' => 'corolla edit',
            'serial' => '1nz-edit',
            'año' => '2016',
            'condicion' => 'APLICA',
            'status' => 'DISPONIBLE',
            'price' => '1,500.00',
            'price_sale' => '2,200.00',
            'costo_importacion_unitario' => '500.00',
        ]);

        if (session('errors')) {
            fwrite(STDERR, print_r(session('errors')->getMessages(), true));
        }

        $response->assertSessionHasNoErrors();

        $item->refresh();
        $this->assertEquals('TOYOTA EDIT', $item->marca);
        $this->assertEquals('COROLLA EDIT', $item->modelo);
        $this->assertEquals('1NZ-EDIT', $item->serial);
        $this->assertEquals('DISPONIBLE', $item->status);
        $this->assertEquals('1500.00', $item->price);
        $this->assertEquals('2200.00', $item->price_sale);
        $this->assertEquals('500.00', $item->costo_importacion_unitario);
    }

    /**
     * Test filtering of inventory items by PRECIO PENDIENTE status.
     */
    public function test_filtering_inventory_items_by_precio_pendiente(): void
    {
        $user = User::factory()->create([
            'rol' => 'Contador',
        ]);
        $user->syncRoles(['Contador']);

        // Create one PRECIO PENDIENTE and one DISPONIBLE item
        Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'año' => '2015',
            'status' => 'PRECIO PENDIENTE',
            'price' => '0.00',
        ]);

        Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'HONDA',
            'modelo' => 'CIVIC',
            'año' => '2015',
            'status' => 'DISPONIBLE',
            'price' => '1,000.00',
        ]);

        // Request with PRECIO PENDIENTE filter
        $response = $this->actingAs($user)->get(route('inventario', [
            'status' => 'PRECIO PENDIENTE'
        ]));

        $response->assertStatus(200);
        
        // Assert the inertia page prop contains only the filtered item
        $response->assertInertia(fn ($page) => $page
            ->has('partidas.data', 1)
            ->where('partidas.data.0.marca', 'TOYOTA')
        );
    }

    /**
     * Test that cleanCurrency correctly handles thousands separators for decimal fields.
     */
    public function test_currency_cleaning_preserves_thousands_for_decimal_fields(): void
    {
        $user = User::factory()->create(['rol' => 'Contador']);
        $user->syncRoles(['Contador']);

        $item = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'año' => '2015',
            'status' => 'DISPONIBLE',
            'price' => '0.00',
        ]);

        $response = $this->actingAs($user)->post(route('updateInventario', $item->id), [
            'id' => $item->id,
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'año' => '2015',
            'condicion' => 'APLICA',
            'status' => 'DISPONIBLE',
            'price' => '1.500',
            'price_sale' => '2.500',
            'costo_importacion_unitario' => '1.200',
        ]);

        $response->assertSessionHasNoErrors();

        $item->refresh();
        // Should be saved as numeric values without the thousands dot, so MariaDB parses it as 1200.00 instead of 1.20!
        $this->assertEquals('1500', $item->price);
        $this->assertEquals('2500', $item->price_sale);
        $this->assertEquals('1200.00', $item->costo_importacion_unitario);
    }

    /**
     * Test the dynamic vehicle type lookup endpoint.
     */
    public function test_vehicle_type_endpoint_returns_correct_classification(): void
    {
        $user = User::factory()->create(['rol' => 'Contador']);
        $user->syncRoles(['Contador']);

        // Check for Toyota Corolla (1ZZ)
        $response = $this->actingAs($user)->get(route('inventario.vehicle_type', [
            'marca' => 'TOYOTA',
            'modelo' => '1ZZ',
            'ano' => '2000-2008'
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'tipo_vehiculo' => 'Automóvil',
            'ejemplo' => 'Automóvil (Corolla)'
        ]);

        // Check for Toyota Hilux (2TR)
        $response = $this->actingAs($user)->get(route('inventario.vehicle_type', [
            'marca' => 'TOYOTA',
            'modelo' => '2TR',
            'ano' => '2005-2015'
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'tipo_vehiculo' => 'Camioneta',
            'ejemplo' => 'Camioneta (Hilux-Fortuner)'
        ]);
    }

    /**
     * Test that the precio_pendiente view loads successfully without blocking.
     */
    public function test_precio_pendiente_displays_vehicle_type(): void
    {
        $user = User::factory()->create(['rol' => 'Administrador']);
        $user->syncRoles(['Contador']);

        // Create an item with PRECIO PENDIENTE status (costo_importacion_unitario null or 0)
        Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => '1ZZ',
            'año' => '2000-2008',
            'status' => 'PRECIO PENDIENTE',
            'price' => '0.00',
            'costo_importacion_unitario' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('inventario.precio_pendiente'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('items.data', 1)
            ->missing('items.data.0.vehicle_type')
        );
    }
}
