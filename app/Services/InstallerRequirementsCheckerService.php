<?php

namespace App\Services;

class InstallerRequirementsCheckerService
{
    private string $minPhpVersion = '8.4.0.0';

    public function check(array $requirements): array
    {
        $results = ['requirements' => [], 'errors' => false];
        foreach ($requirements as $type => $items) {
            foreach ($items as $requirement) {
                $isOk = false;

                if ($type === 'php') {
                    $isOk = extension_loaded($requirement);
                } elseif ($type === 'apache' && function_exists('apache_get_modules')) {
                    $isOk = in_array($requirement, apache_get_modules(), true);
                }

                $results['requirements'][$type][$requirement] = $isOk;
                if (!$isOk) {
                    $results['errors'] = true;
                }
            }
        }

        return $results;
    }

    public function checkPHPVersion(string $minPhpVersion = null): array
    {
        $minVersionPhp     = $minPhpVersion;
        $currentPhpVersion = $this->getPhpVersionInfo();
        $supported         = false;

        if ($minPhpVersion == null) {
            $minVersionPhp = $this->getMinPhpVersion();
        }

        if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
            $supported = true;
        }

        return [
            'full'      => $currentPhpVersion['full'],
            'current'   => $currentPhpVersion['version'],
            'minimum'   => $minVersionPhp,
            'supported' => $supported,
        ];
    }

    private static function getPhpVersionInfo(): array
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full'    => $currentVersionFull,
            'version' => $currentVersion,
        ];
    }

    protected function getMinPhpVersion(): string
    {
        return $this->minPhpVersion;
    }
}
