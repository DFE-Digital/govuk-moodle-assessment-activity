<?php

namespace mod_assessment\Util;

use Doctrine\ORM\EntityManagerInterface;

class EntityManagerProvider
{
    private static ?EntityManagerInterface $entityManager = null;

    public static function setEntityManager(EntityManagerInterface $em): void
    {
        self::$entityManager = $em;
    }

    public static function getEntityManager(): EntityManagerInterface
    {
        if (!self::$entityManager) {
            throw new \RuntimeException('EntityManager has not been set in EntityManagerProvider.');
        }

        return self::$entityManager;
    }
}
