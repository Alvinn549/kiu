<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role_id == Role::ROLE_ADMIN) {
            return $this->adminDashboard();
        } elseif ($user->role_id == Role::ROLE_STAFF) {
            return view('dashboard.staff_index');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    private function adminDashboard()
    {
        return view('dashboard.admin_index');
    }
}
