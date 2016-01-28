<?php

namespace JTH\GreenMT;

class Pipe extends \Threaded {
    public $open;

    public function __construct(bool $open = true)
    {
        $this->open = $open;
    }

    public function isOpen() : bool
    {
        return $this->open;
    }

    public function open()
    {
        $this->open = true;
        return $this;
    }

    public function close()
    {
        $this->open = false;
        return $this;
    }
}