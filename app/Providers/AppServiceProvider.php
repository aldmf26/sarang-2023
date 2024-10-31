<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Blade::directive('role', function ($roles) {
            // Mapping role ke posisi_id
            $roleMapping = [
                'presiden' => 1,
                'grading' => 15,
                'cabut' => 13,
                'bk' => 12,
                'cetak' => 14,
                'wip' => 16,
            ];

            // Ubah string menjadi array untuk pengecekan
            $rolesArray = explode(',', str_replace(['(', ')', ' ', "'"], '', $roles));
            $mappedRoles = array_map(function ($role) use ($roleMapping) {
                return $roleMapping[$role] ?? null; // Mengembalikan null jika role tidak ditemukan
            }, $rolesArray);

            // Menghapus nilai null dari array
            $mappedRoles = array_filter($mappedRoles);

            return "<?php if(Auth::check() && in_array(Auth::user()->posisi_id, [" . implode(',', $mappedRoles) . "])): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
    }
}
