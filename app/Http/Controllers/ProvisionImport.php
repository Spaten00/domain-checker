<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ProvisionImport extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Artisan::call('tanss:import');
        Artisan::call('rrpproxy:import');
        return redirect()->back()->with('status', ['class' => 'success', 'msg' => 'Import war erfolgreich!']);
    }
}
