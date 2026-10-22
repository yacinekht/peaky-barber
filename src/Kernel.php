<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private function isVercel(): bool
    {
        return (bool) ($_SERVER['VERCEL'] ?? $_ENV['VERCEL'] ?? getenv('VERCEL'));
    }

    public function getCacheDir(): string
    {
        return $this->isVercel() ? '/tmp/symfony/cache/' . $this->environment : parent::getCacheDir();
    }

    public function getBuildDir(): string
    {
        return $this->isVercel() ? '/tmp/symfony/build/' . $this->environment : parent::getBuildDir();
    }

    public function getLogDir(): string
    {
        return $this->isVercel() ? '/tmp/symfony/log' : parent::getLogDir();
    }
}