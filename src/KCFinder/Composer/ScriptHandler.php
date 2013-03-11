<?php
/**
 * Based on Sensio\Bundle\DistributionBundle\Composer\ScriptHandler
 * @see https://github.com/sensio/SensioDistributionBundle/blob/master/Composer/ScriptHandler.php
 */

namespace KCFinder\Composer;

use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{
    public static function installAssets($event)
    {
        $options = self::getOptions($event);
        $webDir = $options['kcfinder-web-dir'];
        $dirMode = $options['kcfinder-dir-mode'];
        if ( is_string( $dirMode ) ) {
            $dirMode = octdec( $dirMode );
        }
        $symlink = $relative = false;
        if ($options['kcfinder-assets-install'] == 'symlink') {
            $symlink = true;
        } elseif ($options['kcfinder-assets-install'] == 'relative') {
            $symlink = $relative = true;
        }

        if (!is_dir($webDir)) {
            echo 'The kcfinder-web-dir ('.$webDir.') specified in composer.json was not found in '.getcwd().', can not install assets.'.PHP_EOL;

            return;
        }

        $targetDir = $webDir . '/kcfinder/';

        $filesystem = new Filesystem();
        $filesystem->remove($targetDir);
        $originDir = __DIR__ . '../../../';

        if ($symlink) {
            if ($relative) {
                $relativeOriginDir = $filesystem->makePathRelative($originDir, realpath($targetDir));
            } else {
                $relativeOriginDir = $originDir;
            }
            $filesystem->symlink($relativeOriginDir, $targetDir);
        } else {
            $filesystem->mkdir($targetDir, $dirMode);
            $filesystem->mirror($originDir, $targetDir);
        }
    }

    protected static function getOptions($event)
    {
        $options = array_merge(array(
                'kcfinder-web-dir' => 'web',
                'kcfinder-assets-install' => 'hard',
                'kcfinder-dir-mode' => 0777
            ), $event->getComposer()->getPackage()->getExtra());

        return $options;
    }
}
