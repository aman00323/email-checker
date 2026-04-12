<?php

declare(strict_types=1);

namespace Aman\EmailVerifier;

use Illuminate\Support\ServiceProvider;

class EmailCheckerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/emailchecker.php', 'emailchecker');

        $this->app->singleton('emailchecker', function ($app) {
            $config = $app['config']->get('emailchecker', []);
            $settings = is_array($config) ? $config : [];

            $checker = new EmailChecker();
            $checker->setSmtpProbeEnabled((bool) ($settings['smtp_probe'] ?? true));
            $checker->setSmtpPort((int) ($settings['smtp_port'] ?? 25));
            $checker->setSmtpTimeoutSeconds((int) ($settings['smtp_timeout_seconds'] ?? 5));
            $checker->setFromEmail(is_string($settings['from_email'] ?? null) ? $settings['from_email'] : '');

            return $checker;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/emailchecker.php' => config_path('emailchecker.php'),
        ], 'emailchecker-config');
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['emailchecker'];
    }
}
