<?php
declare(strict_types=1);

namespace App\Application\Settings;

class Settings implements SettingsInterface
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function get(string $key = '', mixed $default = null): mixed
    {
        if (empty($key)) {
            return $this->settings;
        }

        return $this->settings[$key] ?? $default;
    }
}
