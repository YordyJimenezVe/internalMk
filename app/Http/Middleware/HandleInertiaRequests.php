<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? array_merge($request->user()->toArray(), [
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                    'roles' => $request->user()->getRoleNames(),
                ]) : null,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'billing_ids' => fn() => $request->session()->get('billing_ids'),
                'warranty_ids' => fn() => $request->session()->get('warranty_ids'),
                'is_motor' => fn() => $request->session()->get('is_motor'),
            ],
        ]);
    }
}
