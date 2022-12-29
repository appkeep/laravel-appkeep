<?php

namespace Appkeep\Laravel\Events;

/**
 * This is just an empty event we send when you run appkeep:login command.
 * It helps us check if your APPKEEP_KEY is working or not.
 */
class LoginEvent extends AbstractEvent
{
    protected $name = 'login';
}
