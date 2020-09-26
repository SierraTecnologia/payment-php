<?php

namespace SierraTecnologia\Util;

use IteratorAggregate;
use ArrayIterator;

class Set implements IteratorAggregate
{
    private $_elts;

    public function __construct($members = [])
    {
        $this->_elts = [];
        foreach ($members as $item) {
            $this->_elts[$item] = true;
        }
    }

    public function includes($elt): bool
    {
        return isset($this->_elts[$elt]);
    }

    public function add($elt): void
    {
        $this->_elts[$elt] = true;
    }

    public function discard($elt): void
    {
        unset($this->_elts[$elt]);
    }

    /**
     * @return array-key[]
     *
     * @psalm-return list<array-key>
     */
    public function toArray(): array
    {
        return array_keys($this->_elts);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }
}
