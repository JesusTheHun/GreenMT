<?php

namespace JTH\GreenMT\OnDemand;

use JTH\GreenMT\Pipe;
use JTH\GreenMT\PipeAware;
use JTH\GreenMT\VolatileAware;
use Volatile;

abstract class WorkAbstract extends \Threaded implements PipeAware, VolatileAware {
    /**
     * @var Pipe
     */
    public $pipe;
    /**
     * @var Volatile
     */
    public $volatile;

    protected $garbage = 0;

    public function setPipe(Pipe $pipe)
    {
        $this->pipe = $pipe;
    }

    public function setVolatile(Volatile $volatile)
    {
        $this->volatile = $volatile;
    }

    public function setGarbage()
    {
        $this->garbage = 1;
    }

    public function isGarbabge()
    {
        return $this->garbage === 1;
    }

    public function run()
    {
        $v = $this->volatile;

        while ($this->pipe->isOpen()) {
            while (!($r = count($v))) {
                $v->notify();

                if ($this->pipe->isOpen()) {
                    $v->wait();
                } else {
                    break 2;
                }
            }

            $this->processPacket($v->chunk($r));
        }

        $this->setGarbage();
    }

    abstract public function processPacket(array $packet);
}
