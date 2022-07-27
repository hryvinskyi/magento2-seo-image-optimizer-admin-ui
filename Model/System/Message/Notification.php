<?php
/**
 * Copyright (c) 2022. MageCloud.  All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoImageOptimizerAdminUi\Model\System\Message;

use Magento\Framework\Module\Dir;
use Magento\Framework\Notification\MessageInterface;

class Notification implements MessageInterface
{
    private Dir $moduleDir;
    private array $convertors;

    public function __construct(
        Dir $moduleDir,
        array $convertors = ['cavif', 'cwebp', 'magick']
    ) {
        $this->moduleDir = $moduleDir;
        $this->convertors = $convertors;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity(): string
    {
        return 'hryvinskyi_seo_image_optimizer_permissions';
    }

    /**
     * Return module dir
     *
     * @return string
     */
    private function getModuleDir(): string
    {
        return $this->moduleDir->getDir('Hryvinskyi_SeoImageOptimizer');
    }

    /**
     * @inheritDoc
     */
    public function isDisplayed(): bool
    {
        $dir = $this->getModuleDir();

        $result = false;

        foreach ($this->convertors as $convertor) {
            $perms = (int)substr(substr(decoct(fileperms($dir . '/bin/' . $convertor)), 3), 0, 1);

            $result = $result || 7 !== $perms;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        $path = $this->getModuleDir() . '/bin/*';
        return 'You need to grant permissions to binary files in <pre>chmod +x ' . $path . '</pre>';
    }

    /**
     * @inheritDoc
     */
    public function getSeverity(): int
    {
        return self::SEVERITY_CRITICAL;
    }
}
