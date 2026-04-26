<?php

namespace Acelle\Server;

use Illuminate\Support\ServiceProvider as Base;
use App\Library\Facades\Hook;

class ServiceProvider extends Base
{
    public function register()
    {
        defined('ACELLE_SERVER_PLUGIN_FULL_NAME') || define('ACELLE_SERVER_PLUGIN_FULL_NAME', 'acelle/server');
        defined('ACELLE_SERVER_PLUGIN_SHORT_NAME') || define('ACELLE_SERVER_PLUGIN_SHORT_NAME', 'server');

        // Translations: per-language files are materialized under storage/app/data/plugins/acelle/server/lang/
        // by Language::dump() at plugin install time. The 'add_translation_file' hook tells the host how to
        // populate that folder from the plugin's master english file.
        $translationFolder = storage_path('app/data/plugins/acelle/server/lang/');

        Hook::add('add_translation_file', function () use ($translationFolder) {
            return [
                'id'                      => '#acelle/server_translation_file',
                'plugin_name'             => 'acelle/server',
                'file_title'              => 'Acelle Server (Verification Engine) translations',
                'translation_folder'      => $translationFolder,
                'translation_prefix'      => 'server',
                'file_name'               => 'messages.php',
                'master_translation_file' => realpath(__DIR__.'/../resources/lang/en/messages.php'),
            ];
        });

        // Make config('verification_servers.servers') resolvable. Plugin ships its own default pool
        // at config/verification_servers.php; consumers (Orchestrator, Controllers) read it via
        // the global config() helper unchanged.
        $this->mergeConfigFrom(__DIR__.'/../config/verification_servers.php', 'verification_servers');
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'server');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('plugins/acelle/server'),
        ], 'plugin');

        Hook::on('activate_plugin_acelle/server', function () {
            \Artisan::call('migrate', [
                '--path'  => 'storage/app/plugins/acelle/server/database/migrations',
                '--force' => true,
            ]);
        });

        Hook::on('delete_plugin_acelle/server', function () {
            \Artisan::call('migrate:rollback', [
                '--path'  => 'storage/app/plugins/acelle/server/database/migrations',
                '--force' => true,
            ]);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Acelle\Server\Console\CampaignsReport::class,
            ]);
        }
    }
}
