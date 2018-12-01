<?php
/**
 * This file is part of the daikon-cqrs/cqrs project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Tests\EventSourcing;

use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use PHPUnit\Framework\TestCase;

final class DomainEventTest extends TestCase
{
    public function testFromNative()
    {
        /** @var $pizzaWasBaked PizzaWasBaked */
        $pizzaWasBaked = PizzaWasBaked::fromNative([
            'aggregateId' => 'pizza-42-6-23',
            'aggregateRevision' => 1,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ]);
        $this->assertEquals('pizza-42-6-23', $pizzaWasBaked->getAggregateId());
        $this->assertEquals(1, $pizzaWasBaked->getAggregateRevision()->toNative());
        $this->assertEquals([ 'mushrooms', 'tomatoes', 'onions' ], $pizzaWasBaked->getIngredients());
    }

    public function testToNative()
    {
        $pizzaWasBakedArray = [
            'aggregateId' => 'pizza-42-6-23',
            'aggregateRevision' => 1,
            'ingredients' => [ 'mushrooms', 'tomatoes', 'onions' ]
        ];
        $this->assertEquals($pizzaWasBakedArray, PizzaWasBaked::fromNative($pizzaWasBakedArray)->toNative());
    }
}
