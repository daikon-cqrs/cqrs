<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Cqrs\Aggregate;

interface AggregateRootInterface
{
    public static function reconstituteFromHistory(
        AggregateIdInterface $aggregateId,
        DomainEventSequence $history
    ): AggregateRootInterface;

    public function getIdentifier(): AggregateIdInterface;

    public function getRevision(): AggregateRevision;

    public function getTrackedEvents(): DomainEventSequence;

    public function markClean(): AggregateRootInterface;
}
