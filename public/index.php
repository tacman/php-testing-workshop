<?php

declare(strict_types=1);

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

use Symfony\Component\HttpKernel\KernelInterface;

return function (array $context): KernelInterface {
    \assert(\is_string($context['APP_ENV']));

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
