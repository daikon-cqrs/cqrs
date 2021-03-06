<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\EventSourcing\EventStore\Stream;

use Daikon\EventSourcing\Aggregate\AggregateId;
use Daikon\EventSourcing\Aggregate\AggregateIdInterface;
use Daikon\EventSourcing\Aggregate\AggregateRevision;
use Daikon\EventSourcing\Aggregate\Event\DomainEventSequenceInterface;
use Daikon\EventSourcing\EventStore\Commit\Commit;
use Daikon\EventSourcing\EventStore\Commit\CommitInterface;
use Daikon\EventSourcing\EventStore\Commit\CommitSequence;
use Daikon\EventSourcing\EventStore\Commit\CommitSequenceInterface;
use Daikon\Interop\Assertion;
use Daikon\Metadata\MetadataInterface;
use Ds\Vector;

final class Stream implements StreamInterface
{
    private AggregateIdInterface $aggregateId;

    private CommitSequenceInterface $commitSequence;

    private string $commitImplementor;

    public static function fromAggregateId(
        AggregateIdInterface $aggregateId,
        string $commitImplementor = Commit::class
    ): self {
        return new self($aggregateId);
    }

    /** @param array $state */
    public static function fromNative($state): self
    {
        Assertion::keyExists($state, 'aggregateId');
        Assertion::keyExists($state, 'commitSequence');

        return new self(
            AggregateId::fromNative($state['aggregateId']),
            CommitSequence::fromNative($state['commitSequence']),
            $state['commitImplementor'] ?? null
        );
    }

    public function getAggregateId(): AggregateIdInterface
    {
        return $this->aggregateId;
    }

    public function getHeadSequence(): Sequence
    {
        if ($this->isEmpty()) {
            return Sequence::makeInitial();
        }
        return $this->getHead()->getSequence();
    }

    public function getHeadRevision(): AggregateRevision
    {
        if ($this->isEmpty()) {
            return AggregateRevision::makeEmpty();
        }
        return $this->getHead()->getHeadRevision();
    }

    public function appendEvents(DomainEventSequenceInterface $eventLog, MetadataInterface $metadata): self
    {
        return $this->appendCommit(
            ([$this->commitImplementor, 'make'])(
                $this->aggregateId,
                $this->getHeadSequence()->increment(),
                $eventLog,
                $metadata
            )
        );
    }

    public function appendCommit(CommitInterface $commit): self
    {
        $stream = clone $this;
        $stream->commitSequence = $this->commitSequence->push($commit);
        return $stream;
    }

    public function getHead(): CommitInterface
    {
        return $this->commitSequence->getHead();
    }

    public function getCommitRange(Sequence $fromRev, Sequence $toRev = null): CommitSequenceInterface
    {
        return $this->commitSequence->getSlice($fromRev, $toRev ?? $this->getHeadSequence());
    }

    public function count(): int
    {
        return $this->commitSequence->count();
    }

    public function isEmpty(): bool
    {
        return $this->commitSequence->isEmpty();
    }

    public function toNative(): array
    {
        return [
            'commitSequence' => $this->commitSequence->toNative(),
            'aggregateId' => $this->aggregateId->toNative(),
            'commitImplementor' => $this->commitImplementor
        ];
    }

    public function getIterator(): Vector
    {
        return $this->commitSequence->getIterator();
    }

    public function findCommitsSince(AggregateRevision $incomingRevision): CommitSequenceInterface
    {
        $previousCommits = [];
        /** @var CommitInterface $commit */
        foreach ($this as $commit) {
            if ($commit->getTailRevision()->isGreaterThanOrEqual($incomingRevision)) {
                $previousCommits[] = clone $commit;
            }
        }
        return new CommitSequence($previousCommits);
    }

    private function __construct(
        AggregateIdInterface $aggregateId,
        CommitSequenceInterface $commitSequence = null,
        string $commitImplementor = null
    ) {
        $this->aggregateId = $aggregateId;
        $this->commitSequence = $commitSequence ?? CommitSequence::makeEmpty();
        $this->commitImplementor = $commitImplementor ?? Commit::class;
    }
}
