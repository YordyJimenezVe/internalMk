<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckPermissionExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:check-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke permissions that have expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expired = DB::table('permission_expirations')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $record) {
            $modelClass = $record->model_type;
            if ($modelClass == 'users')
                $modelClass = User::class; // Map table names if morphs uses table names
            if ($modelClass == 'roles')
                $modelClass = Role::class;

            // Or if morphs stored full class name (default in Laravel)
            $model = $record->model_type::find($record->model_id);

            if ($model) {
                // Check if they still have it
                if ($model->hasDirectPermission($record->permission_name)) {
                    $model->revokePermissionTo($record->permission_name);
                    $this->info("Revoked {$record->permission_name} from {$record->model_type} ID {$record->model_id}");
                }
            }

            // Remove expiration record
            DB::table('permission_expirations')->where('id', $record->id)->delete();
        }

        $this->info('Expiration check complete.');
    }
}
