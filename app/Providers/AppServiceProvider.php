<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\UnitUsahaLink;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftarkan ActivityLogger sebagai Singleton agar ramah memori
        $this->app->singleton(ActivityLogger::class, function () {
            return new ActivityLogger();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Berikan hak akses Implisit untuk Super Admin/Admin jika menggunakan Spatie Gate
        Gate::before(function ($user, $ability) {
            return method_exists($user, 'hasRole') && $user->hasRole('admin') ? true : null;
        });

        // View Composer untuk membagikan variabel unitUsahaLink dan siteSetting ke Layout
        View::composer(['layouts.public', 'layouts.app', 'layouts.guest', 'layouts.main', 'auth.login', 'components.public.footer', 'components.public.navbar'], function ($view) {
            // Data Link Unit Usaha Active
            $linkData = Cache::remember('active_unit_usaha_link', 3600, function () {
                if (Schema::hasTable('unit_usaha_links')) {
                    $item = UnitUsahaLink::where('is_active', true)->first();
                    if ($item) {
                        $array = $item->toArray();
                        $array['url'] = $item->external_url ?? null;
                        return $array;
                    }
                }
                return null;
            });

            // Data Pengaturan Website Active (Dipanggil via Setting::current() dengan Cache)
            $siteSetting = null;
            if (Schema::hasTable('settings')) {
                $siteSetting = Setting::current();
            }

            $link = $linkData ? (object) $linkData : null;

            $view->with('unitUsahaLink', $link);
            $view->with('siteSetting', $siteSetting);
        });

        // Clear cache secara otomatis ketika data UnitUsahaLink berubah/dihapus
        if (Schema::hasTable('unit_usaha_links')) {
            UnitUsahaLink::saved(function () {
                Cache::forget('active_unit_usaha_link');
            });

            UnitUsahaLink::deleted(function () {
                Cache::forget('active_unit_usaha_link');
            });
        }
    }
}