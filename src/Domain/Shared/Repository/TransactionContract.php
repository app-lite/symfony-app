<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

interface TransactionContract
{
    public function startTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
