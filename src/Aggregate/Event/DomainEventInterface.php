<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\Aggregate\Event;

use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\MessageBus\MessageInterface;

interface DomainEventInterface extends MessageInterface
{
    public function conflictsWith(DomainEventInterface $otherEvent): bool;

    public function getAggregateId(): AggregateIdInterface;

    public function getAggregateRevision(): AggregateRevision;

    public function withAggregateRevision(AggregateRevision $aggregateRevision): self;
}
