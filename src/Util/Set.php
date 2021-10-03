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

    /**
     * @return bool
     */
    public function includes($elt)
    {
        return isset($this->_elts[$elt]);
    }

    /**
     * @return void
     */
    public function add($elt)
    {
        $this->_elts[$elt] = true;
    }

    /**
     * @return void
     */
    public function discard($elt)
    {
        unset($this->_elts[$elt]);
    }

    /**
     * @return (int|string)[]
     *
     * @psalm-return list<array-key>
     */
    public function toArray()
    {
        return array_keys($this->_elts);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }
}
