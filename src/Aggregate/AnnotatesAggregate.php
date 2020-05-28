<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate;

use Daikon\Interop\RuntimeException;
use ReflectionClass;

trait AnnotatesAggregate
{
    public function getAggregateId(): AggregateIdInterface
    {
        return $this->{static::getAnnotatedId()};
    }

    private static function getAnnotatedId(): string
    {
        return static::getAnnotation('id');
    }

    private static function getAnnotatedRevision(): string
    {
        return static::getAnnotation('rev');
    }

    private static function getAnnotation(string $key): string
    {
        $classReflection = new ReflectionClass(static::class);
        foreach (static::getInheritanceTree($classReflection, true) as $curClass) {
            if (!($docComment = $curClass->getDocComment())) {
                continue;
            }
            preg_match("#@$key\((?<$key>\w+)#", $docComment, $matches);
            if (isset($matches[$key])) {
                return trim($matches[$key]);
            }
        }

        throw new RuntimeException("Missing @$key annotation on ".static::class);
    }
}
