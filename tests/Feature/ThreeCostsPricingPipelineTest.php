<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Inventario;
use App\Models\Maintenance;
use App\Models\MaintenanceItem;
use App\Models\Setting;
use App\Models\BillingRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreeCostsPricingPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles and permissions
        $permissions = ['view partida', 'view billing', 'manage billing'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        Role::firstOrCreate(['name' => 'Facturacion'])->syncPermissions($permissions);
        Role::firstOrCreate(['name' => 'Vendedor'])->syncPermissions(['view partida']);
    }

    public function test_recalculate_price_on_inventory_and_maintenance_invoice(): void
    {
        // 1. Create default global setting (utility_percentage = 30)
        Setting::set('utility_percentage', 30);

        // 2. Create motor in PRECIO PENDIENTE
        $motor = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'status' => 'PRECIO PENDIENTE',
            'costo_importacion_unitario' => 1000.00,
            'price' => 0.00,
        ]);

        // Recalculate price: (1000 + 0) * 1.30 = 1300
        $motor->recalculatePrice();
        $this->assertEquals(1300.00, $motor->price);

        // 3. Create a maintenance with workshop invoice for the motor
        $maintenance = Maintenance::create([
            'partida_id' => $motor->id,
            'status' => 'INICIADO',
            'tipo' => 'REPARACION',
            'codigo' => 'MNT-001',
        ]);

        // Create item with FACTURA (which should be added to the cost)
        $itemFactura = MaintenanceItem::create([
            'maintenance_id' => $maintenance->id,
            'description' => 'Repuesto con Factura',
            'quantity' => 1,
            'unit_price' => 200.00,
            'base_imponible' => 200.00,
            'document_type' => 'FACTURA',
            'document_number' => 'FAC-123',
        ]);

        // Item cost is recalculated:
        // Costo de Taller con Facturas = 200
        // Price = (Costo Importacion (1000) + Costo Taller con Factura (200)) * (1 + 30/100) = 1200 * 1.30 = 1560
        $motor->refresh();
        $this->assertEquals(1560.00, $motor->price);

        // Create item with NOTA DE ENTREGA (should NOT be added to the cost)
        $itemNota = MaintenanceItem::create([
            'maintenance_id' => $maintenance->id,
            'description' => 'Repuesto con Nota',
            'quantity' => 1,
            'unit_price' => 150.00,
            'base_imponible' => 150.00,
            'document_type' => 'NOTA',
            'document_number' => 'NOT-123',
        ]);

        // Cost should remain 1560 since it is not a FACTURA
        $motor->refresh();
        $this->assertEquals(1560.00, $motor->price);
    }

    public function test_update_utility_recalculates_all_inventory(): void
    {
        Setting::set('utility_percentage', 30);

        $motor = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'status' => 'DISPONIBLE',
            'costo_importacion_unitario' => 1000.00,
            'price' => 1300.00,
        ]);

        $user = User::factory()->create(['rol' => 'Facturacion']);
        $user->syncRoles(['Facturacion']);

        // Update utility percentage to 40%
        $response = $this->actingAs($user)->post(route('settings.update_utility'), [
            'utility_percentage' => 40
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(40, Setting::get('utility_percentage'));

        // Motor price should now be: 1000 * 1.4 = 1400
        $motor->refresh();
        $this->assertEquals(1400.00, $motor->price);
    }

    public function test_billing_request_updates_sale_price(): void
    {
        $motor = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'status' => 'DISPONIBLE',
            'price' => 1300.00,
            'price_sale' => 0.00,
        ]);

        $vendedor = User::factory()->create(['rol' => 'Vendedor']);
        $vendedor->syncRoles(['Vendedor']);

        // Send billing request
        $response = $this->actingAs($vendedor)->post(route('billing.requests.store'), [
            'partida_id' => $motor->id,
            'quantity' => 1,
            'price' => 1800.00,
            'client_name' => 'John Doe',
        ]);

        $response->assertSessionHasNoErrors();

        // Motor price_sale should be updated
        $motor->refresh();
        $this->assertEquals(1800.00, $motor->price_sale);

        // Find created billing request
        $req = BillingRequest::where('partida_id', $motor->id)->first();
        $this->assertNotNull($req);
        $this->assertEquals(1800.00, $req->price);

        // Now test updating the billing request
        $admin = User::factory()->create(['rol' => 'Facturacion']);
        $admin->syncRoles(['Facturacion']);

        $responseUpdate = $this->actingAs($admin)->put(route('billing.requests.update', $req->id), [
            'quantity' => 1,
            'price' => 1950.00,
            'client_name' => 'John Doe Changed',
        ]);

        $responseUpdate->assertSessionHasNoErrors();

        $motor->refresh();
        $this->assertEquals(1950.00, $motor->price_sale);
    }

    public function test_precio_pendiente_index_and_update_with_various_statuses(): void
    {
        // 1. Create a motor in PRECIO PENDIENTE with no cost
        $motor1 = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'status' => 'PRECIO PENDIENTE',
            'costo_importacion_unitario' => 0.00,
            'price' => 0.00,
        ]);

        // 2. Create another motor in DISPONIBLE with no cost
        $motor2 = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'HONDA',
            'modelo' => 'CIVIC',
            'status' => 'DISPONIBLE',
            'costo_importacion_unitario' => 0.00,
            'price' => 0.00,
        ]);

        // 3. Create another motor in EN MANTENIMIENTO with no cost
        $motor3 = Inventario::create([
            'tipo' => 'MOTOR 3/4',
            'origen' => 'NACIONAL',
            'marca' => 'FORD',
            'modelo' => 'FIESTA',
            'status' => 'EN MANTENIMIENTO',
            'costo_importacion_unitario' => 0.00,
            'price' => 0.00,
        ]);

        // 4. Create an autopart in PRECIO PENDIENTE with no cost (should be returned)
        $autopart1 = Inventario::create([
            'tipo' => 'AUTOPARTE',
            'origen' => 'NACIONAL',
            'marca' => 'CHEVROLET',
            'modelo' => 'AVEO',
            'status' => 'PRECIO PENDIENTE',
            'costo_importacion_unitario' => 0.00,
            'price' => 0.00,
        ]);

        // 5. Create an autopart in DISPONIBLE with no cost (should NOT be returned)
        $autopart2 = Inventario::create([
            'tipo' => 'AUTOPARTE',
            'origen' => 'NACIONAL',
            'marca' => 'NISSAN',
            'modelo' => 'SENTRA',
            'status' => 'DISPONIBLE',
            'costo_importacion_unitario' => 0.00,
            'price' => 0.00,
        ]);

        $user = User::factory()->create(['rol' => 'Facturacion']);
        $user->syncRoles(['Facturacion']);

        // Request the precio-pendiente index page
        $response = $this->actingAs($user)->get(route('inventario.precio_pendiente'));
        $response->assertStatus(200);

        // Verify only 4 items (3 motors + autopart1) are returned
        $response->assertInertia(fn ($page) => $page
            ->has('items', 4)
            ->where('items.0.marca', 'TOYOTA')
            ->where('items.1.marca', 'HONDA')
            ->where('items.2.marca', 'FORD')
            ->where('items.3.marca', 'CHEVROLET')
        );

        // 6. Update the cost of $motor3 (status EN MANTENIMIENTO) and verify status does NOT change
        $responseUpdate = $this->actingAs($user)->post(route('inventario.precio_pendiente.update', $motor3->id), [
            'costo_importacion_unitario' => '450.00'
        ]);
        $responseUpdate->assertSessionHasNoErrors();

        $motor3->refresh();
        $this->assertEquals(450.00, $motor3->costo_importacion_unitario);
        $this->assertEquals('EN MANTENIMIENTO', $motor3->status);

        // 7. Update the cost of $motor1 (status PRECIO PENDIENTE) and verify status changes to DISPONIBLE
        $responseUpdate2 = $this->actingAs($user)->post(route('inventario.precio_pendiente.update', $motor1->id), [
            'costo_importacion_unitario' => '500.00'
        ]);
        $responseUpdate2->assertSessionHasNoErrors();

        $motor1->refresh();
        $this->assertEquals(500.00, $motor1->costo_importacion_unitario);
        $this->assertEquals('DISPONIBLE', $motor1->status);
    }
}
