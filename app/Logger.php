<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class Logger
{
    private static $logger;

    public static function warning(string $message, array $context = []): void
    {
        self::setupLoggerIfRequired();
        self::$logger->warning($message, $context);
    }

    public static function setupLoggerIfRequired(): void
    {
        if (!self::$logger) {
            self::$logger = new MonologLogger('app');
            self::$logger->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/app.log', MonologLogger::WARNING));
        }
    }




}
