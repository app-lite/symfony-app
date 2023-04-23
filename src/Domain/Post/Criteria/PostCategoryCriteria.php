<?php

declare(strict_types=1);

namespace App\Domain\Post\Criteria;

use App\Domain\Post\Constant\PostCategoryEnum;
use App\Domain\Shared\Constant\SharedOrderEnum;

class PostCategoryCriteria
{
    private ?string $key = null;
    /** @var string[] */
    private array $orderList = [];
    /** @var string[] */
    private array $statusList = [];

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function addKey(string $key): self
    {
        $this->guardKey($key);
        $this->key = $key;

        return $this;
    }

    public function removeKey(): self
    {
        $this->key = null;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getStatusList(): array
    {
        return $this->statusList;
    }

    public function addStatus(string $status): self
    {
        $this->guardStatus($status);
        $this->statusList[$status] = $status;

        return $this;
    }

    /**
     * @param string[] $statusList
     * @return $this
     */
    public function addStatusList(array $statusList): self
    {
        foreach ($statusList as $status) {
            $this->guardStatus($status);
            $this->statusList[$status] = $status;
        }

        return $this;
    }

    public function removeStatus(string $status): self
    {
        unset($this->statusList[$status]);

        return $this;
    }

    /**
     * @param string[] $statusList
     * @return $this
     */
    public function removeStatusList(array $statusList): self
    {
        foreach ($statusList as $status) {
            unset($this->statusList[$status]);
        }

        return $this;
    }

    public function guardStatus(string $status): void
    {
        if (!array_key_exists($status, PostCategoryEnum::STATUS_LIST)) {
            throw new \InvalidArgumentException('Incorrect status');
        }
    }

    public function guardOrderColumn(string $column): void
    {
        if (!in_array($column, PostCategoryEnum::ORDER_FIELD_LIST)) {
            throw new \InvalidArgumentException('Incorrect column!');
        }
    }

    public function addOrder(string $column, ?string $direction = 'asc'): self
    {
        $this->guardOrderColumn($column);
        $direction = strtolower($direction);

        $this->guardOrderDirection($direction);

        $this->orderList[$column] = $direction;

        return $this;
    }

    public function removeOrder(string $column): self
    {
        unset($this->orderList[$column]);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOrderList(): array
    {
        return $this->orderList;
    }

    public function guardKey(string $key): void
    {
        if (!in_array($key, PostCategoryEnum::KEY_LIST)) {
            throw new \InvalidArgumentException('Incorrect key!');
        }
    }

    public function guardOrderDirection(string $direction): void
    {
        if (!in_array($direction, SharedOrderEnum::ORDER_DIRECTION_LIST, true)) {
            throw new \InvalidArgumentException('Order direction must be "asc" or "desc".');
        }
    }

    public static function create(): self
    {
        return new self();
    }
}
