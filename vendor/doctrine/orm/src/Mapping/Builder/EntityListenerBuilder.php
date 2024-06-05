<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping\Builder;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;

use function class_exists;
use function get_class_methods;

/**
 * Builder for entity listeners.
 */
class EntityListenerBuilder
{
    /** Hash-map to handle event names. */
    private const EVENTS = [
        Events::preRemove   => true,
        Events::postRemove  => true,
        Events::prePersist  => true,
        Events::postPersist => true,
        Events::preUpdate   => true,
        Events::postUpdate  => true,
        Events::postLoad    => true,
        Events::preFlush    => true,
    ];

    /**
     * Lookup the entity class to find methods that match to event lifecycle names
     *
     * @param ClassMetadata $metadata  The entity metadata.
     * @param string        $className The listener class name.
     *
     * @throws MappingException When the listener class not found.
     */
    public static function bindEntityListener(ClassMetadata $metadata, string $className): void
    {
        $class = $metadata->fullyQualifiedClassName($className);

        if (! class_exists($class)) {
            throw MappingException::entityListenerClassNotFound($class, $className);
        }

        foreach (get_class_methods($class) as $method) {
            if (! isset(self::EVENTS[$method])) {
                continue;
            }

            $metadata->addEntityListener($method, $class, $method);
        }
    }
}
