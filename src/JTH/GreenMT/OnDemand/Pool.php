<?php

namespace JTH\GreenMT\OnDemand;

use JTH\GreenMT\Pipe;
use JTH\GreenMT\PipeAware;
use JTH\GreenMT\VolatileAware;
use Worker;
use Volatile;
use Threaded;

class Pool extends \Pool {

    protected $packet;
    protected $pipe;

    public function __construct($size, $class = Worker::class, array $ctor = array())
    {
        $this->pipe = new Pipe();
        $this->packet = new Volatile();
        parent::__construct($size, $class, $ctor);
    }

    public function getPipe()
    {
        return $this->pipe;
    }

    public function getPacket()
    {
        return $this->packet;
    }

    public function submit(Threaded $threaded)
    {
        if (
            !($threaded instanceof PipeAware) or
            !($threaded instanceof VolatileAware)
        ) {
            throw new \InvalidArgumentException("Threadeds submitted to OnDemandePool should implement both PipeAware and VolatileAware");
        }

        $threaded->setPipe($this->getPipe());
        $threaded->setVolatile($this->getPacket());

        parent::submit($threaded);
    }


    public function dispatch(Fetchable $fetchable, $packetSize)
    {
        $packet = $this->getPacket();
        $pipe = $this->getPipe();

        $packet->synchronized(function($packetSize) use ($fetchable, $packet, $pipe) {
            $pipe->open();

            do {
                $packet->wait();

                for ($i = 0; $i < $packetSize; $i++) {
                    $row = $fetchable->fetch();
                    if (false !== $row) {
                        $packet[] = (array) $row;
                    } else {
                        break;
                    }
                }

                $packet->notify();
            } while (false !== $row);

            $pipe->close();
        }, $packetSize);
    }
}