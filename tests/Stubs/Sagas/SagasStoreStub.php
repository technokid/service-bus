<?php

/**
 * PHP Service Bus (publish-subscribe pattern implementation)
 * Supports Saga pattern and Event Sourcing
 *
 * @author  Maksim Masiukevich <desperado@minsk-info.ru>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace Desperado\ServiceBus\Tests\Stubs\Sagas;

use Amp\Success;
use Desperado\ServiceBus\Sagas\SagaId;
use Desperado\ServiceBus\Sagas\SagaStore\SagasStore;
use Desperado\ServiceBus\Sagas\SagaStore\StoredSaga;

/**
 *
 */
class SagasStoreStub implements SagasStore
{
    /**
     * @inheritdoc
     */
    public function save(StoredSaga $savedSaga): \Generator
    {
        return yield new Success();
    }

    /**
     * @inheritdoc
     */
    public function update(StoredSaga $savedSaga): \Generator
    {
        return yield new Success();
    }

    /**
     * @inheritdoc
     */
    public function load(SagaId $id): \Generator
    {
        return yield new Success();
    }

    /**
     * @inheritdoc
     */
    public function remove(SagaId $id): \Generator
    {
        return yield new Success();
    }
}
