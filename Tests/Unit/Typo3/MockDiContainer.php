<?php

namespace BR\Toolkit\Tests\Unit\Typo3;

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MockDiContainer implements ContainerInterface
{
    private static $storage = [];

    /** @var ?ContainerInterface */
    private $baseContainer;

    /**
     * @param ContainerInterface|null $baseContainer
     */
    public function __construct(?ContainerInterface $baseContainer = null)
    {
        $this->baseContainer = $baseContainer;
    }

    /**
     * @return static
     */
    public static function injectGeneralUtility(): self
    {
        if (method_exists(GeneralUtility::class,'getContainer')) {
            try {
                $container = GeneralUtility::getContainer();
            } catch (\LogicException $e) {
                $container = null;
            }
            $container = new self($container);
            GeneralUtility::purgeInstances();
            GeneralUtility::setContainer($container);
        } else {
            $container = new self(null);
            GeneralUtility::purgeInstances();
        }

        return $container;
    }

    /**
     * @param string $id
     * @param $object
     */
    public function set(string $id, $object): void
    {
        if (method_exists(GeneralUtility::class,'getContainer')) {
            self::$storage[$id] = $object;
        } else {

            if ($object instanceof SingletonInterface) {
                GeneralUtility::setSingletonInstance($id, $object);
            } else {
                GeneralUtility::addInstance($id, $object);
            }

        }
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        if (isset(self::$storage[$id])) {
            return self::$storage[$id];
        }

        if ($this->baseContainer) {
            return $this->baseContainer->get($id);
        }

        return null;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if (isset(self::$storage[$id])) {
            return true;
        }

        if ($this->baseContainer) {
            return $this->baseContainer->has($id);
        }

        return false;
    }
}