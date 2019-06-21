<?php
declare (strict_types = 1);
namespace Iggyvolz\RewindableIterator;

/**
 * Make any iterator rewindable without needing to get the new values again
 */
class RewindableIterator implements \Iterator
{
    /**
     * @var \Iterator The original iterator
     */
    private $it;
    /**
     * @var array[] Iterator ID to array of cached keys
     */
    private static $keys = [];
    /**
     * @var array[] Iterator ID to array of cached values
     */
    private static $values = [];
    /**
     * @var int Current position of the current iterator
     * Starts at -1 so we can call next() in the constructor to do setup
     */
    private $pos = -1;
    public function __construct(iterable $it)
    {
        if (is_array($it)) {
            $it = new \ArrayIterator($it);
        }
        // is there a better way to do this?
        while ($it instanceof \IteratorAggregate) {
            $it = $it->getIterator();
        }
        $this->it = $it;
        $this->next();
    }
    public function current()
    {
        return self::$values[spl_object_id($this->it)][$this->pos];
    }
    public function key()
    {
        return self::$keys[spl_object_id($this->it)][$this->pos];
    }
    public function next(): void
    {
        $this->pos++;
        $this->it->next();
        if($this->it->valid()){
            self::$values[spl_object_id($this->it)][$this->pos]=$this->it->current();
            self::$keys[spl_object_id($this->it)][$this->pos]=$this->it->key();
        }
     }
    public function rewind(): void
    {
        $this->pos = 0;
    }
    public function valid(): bool
    {
        return array_key_exists($this->pos, self::$values[spl_object_id($this->it)]);
    }
}
