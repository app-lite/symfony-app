<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Shared\Repository;

use App\Domain\Shared\Repository\TransactionContract;
use Doctrine\DBAL\Connection;

class SymfonyTransaction implements TransactionContract
{
    public function __construct(private Connection $db)
    {
    }

    public function startTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollBack();
    }
}
