<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (
                $user &&
                Hash::check($request->password, $user->password)
            ) {

                if ($user->settings()->where('key', 'active_company')->first()) {
                    session(['company_id' => $user->settings()->where('key', 'active_company')->first()->value]);
                    // session(['year_id'=>$user->settings()->where('key', 'active_year')->first()->value]);
                }
                if ($user->settings()->where('key', 'active_year')->first()) {
                    // session(['company_id'=>$user->settings()->where('key', 'active_company')->first()->value]);
                    session(['year_id' => $user->settings()->where('key', 'active_year')->first()->value]);
                    $active_yr = Year::where('id', session('year_id'))->first();
                    if ($active_yr->users()->first()) {
                        session(['team_id' => $active_yr->users()->first()->id]);
                    } else {
                        session(['team_id' => null]);
                    }
                }
                return $user;
            }
        });
    }

    /**
     * Configure the permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
