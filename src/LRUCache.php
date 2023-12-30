<?php

/**
 * Created by PhpStorm.
 * User: LinFei
 * Created time 2023/12/11 11:10:44
 * E-mail: fly@eyabc.cn
 */
declare (strict_types=1);

namespace Lynnfly;

use InvalidArgumentException;

class LRUCache
{
    /**
     * The front of the array contains the LRU element
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Create a LRU Cache
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        /**
         * Cache capacity
         * @var int $capacity
         */
        protected int $capacity
    )
    {
        if ($capacity <= 0) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Get the value cached with this key
     *
     * @param int|string $key Cache key
     * @param mixed|null $default The value to be returned if key not found. (Optional)
     * @return mixed
     */
    public function get(int|string $key, mixed $default = null): mixed
    {
        if (isset($this->data[$key])) {
            $this->recordAccess($key);
            return $this->data[$key];
        } else {
            return $default;
        }
    }

    /**
     * Set something in the cache
     *
     * @param int|string $key Cache key
     * @param mixed $value The value to cache
     */
    public function set(int|string $key, mixed $value): void
    {
        if (isset($this->data[$key])) {
            $this->data[$key] = $value;
            $this->recordAccess($key);
        } else {
            $this->data[$key] = $value;
            if ($this->size() > $this->capacity) {
                // remove least recently used element (front of array)
                reset($this->data);
                unset($this->data[key($this->data)]);
            }
        }
    }

    /**
     * Set something in the cache if it hasn't been set already
     * @param int|string $key
     * @param mixed|callable $value
     * @return mixed
     */
    public function getOrSet(int|string $key, mixed $value): mixed
    {
        if (!$this->has($key)) {
            $this->set($key, is_callable($value) ? $value() : $value);
        }

        return $this->get($key);
    }

    /**
     * Get the number of elements in the cache
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->data);
    }

    /**
     * Does the cache contain an element with this key
     *
     * @param int|string $key The key
     * @return bool
     */
    public function has(int|string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove the element with this key.
     *
     * @param int|string $key The key
     * @return mixed Value or null if not set
     */
    public function remove(int|string $key): mixed
    {
        if (isset($this->data[$key])) {
            $value = $this->data[$key];
            unset($this->data[$key]);
            return $value;
        } else {
            return null;
        }
    }

    /**
     * Clear the cache
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Moves the element from current position to end of array
     *
     * @param int|string $key The key
     */
    protected function recordAccess(int|string $key)
    {
        $value = $this->data[$key];
        unset($this->data[$key]);
        $this->data[$key] = $value;
    }

    /**
     * Get the cache data
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
