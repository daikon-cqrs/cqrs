<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\EventSourcing\EventStore;

use Daikon\EventSourcing\Aggregate\DomainEventInterface;
use Daikon\EventSourcing\Aggregate\DomainEventSequence;
use Exception;

final class ConcurrencyRaceLost extends Exception
{
    /** @var StreamId */
    private $streamId;

    /** @var  DomainEventInterface */
    private $lostEvents;

    public function __construct(StreamId $streamId, DomainEventSequence $lostEvents)
    {
        $this->streamId = $streamId;
        $this->lostEvents = $lostEvents;
        parent::__construct('Unable to catchup on stream: '.$this->streamId);
    }

    public function getStreamId(): StreamId
    {
        return $this->streamId;
    }

    public function getLostEvents(): DomainEventSequence
    {
        return $this->lostEvents;
    }
}