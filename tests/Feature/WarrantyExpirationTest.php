<?php

namespace Tests\Feature;

use App\Models\Billing;
use App\Models\Bitacora;
use App\Models\Inventario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarrantyExpirationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

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

        $role = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $this->admin = User::factory()->create(['rol' => 'Administrador']);
        $this->admin->syncRoles(['Administrador']);
    }

    private function makeBillForType(string $tipo, string $fecha = '2026-11-01', ?int $warrantyMonths = null): Billing
    {
        $item = Inventario::create([
            'tipo' => $tipo,
            'origen' => 'IMPORTADO',
            'marca' => 'TOYOTA',
            'modelo' => 'COROLLA',
            'serial' => 'SER-1234',
            'año' => '2018',
            'status' => 'VENDIDO',
            'price' => '1000.00',
        ]);

        return Billing::create([
            'partida_id' => $item->id,
            'fecha' => $fecha,
            'hora' => '10:00:00',
            'numero_factura' => 'FAC-001',
            'numero_control' => 'CTL-001',
            'client_name' => 'CLIENTE PRUEBA',
            'client_cedula' => 'V-12345678',
            'total' => 1000,
            'warranty_months' => $warrantyMonths,
        ]);
    }

    public function test_default_warranty_months_by_category(): void
    {
        $motorBill = $this->makeBillForType('MOTOR 7/8');
        $cajaBill = $this->makeBillForType('CAJA AUTOMATICA');
        $camaraBill = $this->makeBillForType('CÁMARA');

        $this->assertEquals('MOTOR', $motorBill->resolveWarrantyCategory());
        $this->assertEquals(3, $motorBill->effectiveWarrantyMonths());

        $this->assertEquals('CAJA', $cajaBill->resolveWarrantyCategory());
        $this->assertEquals(2, $cajaBill->effectiveWarrantyMonths());

        $this->assertEquals('CAMARA', $camaraBill->resolveWarrantyCategory());
        $this->assertEquals(1, $camaraBill->effectiveWarrantyMonths());
    }

    public function test_update_warranty_months_for_motor_caja_and_camara_within_1_to_4_months(): void
    {
        $motorBill = $this->makeBillForType('MOTOR COMPLETO');
        $cajaBill = $this->makeBillForType('CAJA');
        $camaraBill = $this->makeBillForType('CAMARA');

        // Motor -> 4 meses
        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $motorBill->id), ['warranty_months' => 4])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'warranty_months' => 4,
                'warranty_days' => 120,
                'warranty_category' => 'MOTOR',
            ]);
        $this->assertEquals(4, $motorBill->fresh()->warranty_months);

        // Caja -> 3 meses
        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $cajaBill->id), ['warranty_months' => 3])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'warranty_months' => 3,
                'warranty_days' => 90,
                'warranty_category' => 'CAJA',
            ]);
        $this->assertEquals(3, $cajaBill->fresh()->warranty_months);

        // Cámara -> 2 meses
        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $camaraBill->id), ['warranty_months' => 2])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'warranty_months' => 2,
                'warranty_days' => 60,
                'warranty_category' => 'CAMARA',
            ]);
        $this->assertEquals(2, $camaraBill->fresh()->warranty_months);

        // Bitácora recorded the updates
        $this->assertTrue(Bitacora::where('description', 'like', '%warranty_months%')->exists());
    }

    public function test_update_warranty_rejects_values_below_1_or_above_4_months(): void
    {
        $motorBill = $this->makeBillForType('MOTOR');

        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $motorBill->id), ['warranty_months' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['warranty_months']);

        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $motorBill->id), ['warranty_months' => 5])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['warranty_months']);

        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $motorBill->id), ['warranty_months' => 12])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['warranty_months']);
    }

    public function test_update_warranty_rejects_non_eligible_items(): void
    {
        $repuestoBill = $this->makeBillForType('REPUESTO ALTERNADOR');

        $this->actingAs($this->admin)
            ->postJson(route('billing.warranty.update', $repuestoBill->id), ['warranty_months' => 2])
            ->assertStatus(422);
    }

    public function test_warranty_blade_renders_custom_months_and_clamps_to_max_4(): void
    {
        $bill = $this->makeBillForType('CAJA', '2026-11-01', 4);

        $html = view('reports.warranty', [
            'bill' => $bill->load(['partida', 'secondPartida']),
            'warrantyCategory' => 'CAJA',
            'warrantyMonths' => $bill->effectiveWarrantyMonths(),
            'warrantyDays' => $bill->effectiveWarrantyMonths() * 30,
        ])->render();

        $this->assertStringContainsString('120 DÍAS / 4 MESES PARA CAJA DE CAMBIOS', $html);
        $this->assertStringContainsString('01/03/2027', $html); // 2026-11-01 + 120 days

        // Ensure clamping never exceeds 4 months even if forced
        $this->assertEquals(4, $bill->effectiveWarrantyMonths(10, 'CAJA'));
        $this->assertEquals(1, $bill->effectiveWarrantyMonths(0, 'CAJA'));
    }

    public function test_warranty_pdf_endpoint_streams_pdf_with_clamped_months(): void
    {
        $bill = $this->makeBillForType('CAMARA', '2026-11-01');

        $response = $this->actingAs($this->admin)
            ->get(route('billing.warranty', ['id' => $bill->id, 'months' => 4]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }
}
