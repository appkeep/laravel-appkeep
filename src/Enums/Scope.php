<?php

namespace Appkeep\Laravel\Enums;

class Scope
{
    /**
     * Use this scope for checks that must be run once across your entire application.
     * It makes sense to use global scope while checking 3rd party services and APIs.
     *
     * Example: Check Stripe status.
     */
    const GLOBAL = 'global';

    /**
     * This scope is ideal for when you're checking server resources.
     * If your app is deployed on multiple servers, the check will run on every server.
     *
     * Example: Check disk space.
     */
    const SERVER = 'server';

    /**
     * The default scope. Runs the check on every instance of your app.
     * If the global and server scopes don't fit, then this is a safe default.
     *
     * Example: Check if app can connect to DB.
     */
    const INSTANCE = 'instance';
}
