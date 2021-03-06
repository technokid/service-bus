<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation).
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Services\Contracts;

/**
 *
 */
interface ValidationFailedEvent
{
    /**
     * List of validate violations:.
     *
     * [
     *    'propertyPath' => [
     *        0 => 'some message',
     *        ....
     *    ]
     * ]
     *
     * @psalm-param array<string, array<int, string>> $violations
     */
    public static function create(string $correlationId, array $violations): self;

    /**
     * Receive request correlation id (Message trace id).
     */
    public function correlationId(): string;

    /**
     * Receive list of validate violations.
     *
     * @psalm-return array<string, array<int, string>>
     */
    public function violations(): array;
}
