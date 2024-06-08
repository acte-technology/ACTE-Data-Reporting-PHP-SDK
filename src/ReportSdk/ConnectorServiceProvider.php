<?php

// Laravel Service Provider

namespace ReportSdk;

use Illuminate\Support\ServiceProvider;

/**
 * Class ConnectorServiceProvider
 */
class ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('reportSdkConnector', Connector::class);
    }
}
