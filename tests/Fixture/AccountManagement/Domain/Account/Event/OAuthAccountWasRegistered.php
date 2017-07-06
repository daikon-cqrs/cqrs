<?php

namespace Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Event;

use Daikon\Cqrs\Aggregate\AggregateId;
use Daikon\Cqrs\Aggregate\AggregateIdInterface;
use Daikon\Cqrs\Aggregate\AggregateRevision;
use Daikon\Cqrs\Aggregate\DomainEvent;
use Daikon\MessageBus\MessageInterface;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Account;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\Command\RegisterOauthAccount;
use Daikon\Tests\Cqrs\Fixture\AccountManagement\Domain\Account\ValueObject\AccessRole;

final class OauthAccountWasRegistered extends DomainEvent
{
    /** @var AccessRole */
    private $role;

    public static function viaCommand(RegisterOauthAccount $registration): self
    {
        return new static($registration->getAggregateId(), $registration->getRole());
    }

    public static function fromArray(array $nativeArray): MessageInterface
    {
        return new self(
            AggregateId::fromNative($nativeArray["aggregateId"]),
            AccessRole::fromNative($nativeArray["role"])
        );
    }

    public static function getAggregateRootClass(): string
    {
        return Account::class;
    }

    public function getRole(): AccessRole
    {
        return $this->role;
    }

    public function toArray(): array
    {
        return array_merge([
            "role" => $this->role->toNative(),
        ], parent::toArray());
    }

    protected function __construct(
        AggregateIdInterface $aggregateId,
        AccessRole $role,
        AggregateRevision $aggregateRevision = null
    ) {
        parent::__construct($aggregateId, $aggregateRevision);
        $this->role = $role;
    }
}
