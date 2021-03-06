<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/event-sourcing project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Tests\EventSourcing;

use Daikon\Tests\EventSourcing\Aggregate\Mock\PizzaWasBaked;
use PHPUnit\Framework\TestCase;

final class DomainEventTest extends TestCase
{
    public function testFromNative(): void
    {
        $ingredients = ['mushrooms', 'tomatoes', 'onions'];
        $pizzaWasBaked = PizzaWasBaked::fromNative([
            'pizzaId' => 'pizza-42-6-23',
            'revision' => 1,
            'ingredients' => $ingredients
        ]);
        $this->assertEquals('pizza-42-6-23', $pizzaWasBaked->getPizzaId());
        $this->assertEquals(1, $pizzaWasBaked->getRevision()->toNative());
        $this->assertEquals($ingredients, $pizzaWasBaked->getIngredients()->toNative());
    }

    public function testToNative(): void
    {
        $pizzaWasBakedArray = [
            'pizzaId' => 'pizza-42-6-23',
            'revision' => 1,
            'ingredients' => ['mushrooms', 'tomatoes', 'onions']
        ];
        $this->assertEquals($pizzaWasBakedArray, PizzaWasBaked::fromNative($pizzaWasBakedArray)->toNative());
    }
}
