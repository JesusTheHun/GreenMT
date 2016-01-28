<?php

namespace JTH\GreenMT\Synchronized;

use JTH\GreenMT\VolatileAware;
use Volatile;

class Work extends \Threaded implements VolatileAware
{

    /**
     * @var int Dummy work ID
     */
    protected $id;
    /**
     * @var $resultBag Volatile Result container passed around threads
     */
    protected $resultBag;

    /**
     * @var bool Should this work be collected for garbage ?
     */
    protected $garbage = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setVolatile(Volatile $volatile)
    {
        $this->resultBag = $volatile;
        return $this;
    }

    public function getResultBag()
    {
        return $this->resultBag;
    }

    /**
     * @return $this Set this work ready to be collected for garbage
     */
    public function setGarbage()
    {
        $this->garbage = 1;
        return $this;
    }

    /**
     * @return bool Is this work ready to be collected for garbage ?
     */
    public function isGarbage() : bool
    {
        return $this->garbage === 1;
    }

    public function run()
    {
        $this->setGarbage();
    }

    public function send($result)
    {
        $this->resultBag->synchronized(function (Volatile $resultBag, $workResult) {
            $resultBag[$this->getId()] = (array)$workResult;
            $resultBag->notify();
        }, $this->getResultBag(), $result);
    }
}