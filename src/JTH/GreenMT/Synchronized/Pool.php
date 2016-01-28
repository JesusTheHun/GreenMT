<?php

namespace JTH\GreenMT\Synchronized;

use JTH\GreenMT\VolatileAware;

class Pool extends \Pool {

    /**
     * @var int Pool's lifetime submitted works count
     */
    protected $submitted = 0;
    /**
     * @var int Pool's lifetime collected works count
     */
    protected $collected = 0;

    protected $resultBag;

    public function __construct($size, $clazz = \Worker::class, $ctor = array())
    {
        $this->resultBag = new \Volatile;
        parent::__construct($size, $clazz, $ctor);
    }

    /**
     * @param Work $threaded Submit the work to the pool and will be dispatched among Pool's Workers
     * @return int|void
     */
    public function submit(\Threaded $threaded)
    {
        if (!($threaded instanceof VolatileAware)) {
            throw new \InvalidArgumentException("Threadeds submitted to OnDemandePool should implement both PipeAware and VolatileAware");
        }

        ++$this->submitted;
        $threaded->setVolatile($this->getResultBag());
        parent::submit($threaded);
    }

    public function getResultBag()
    {
        return $this->resultBag;
    }

    /**
     * @return int Return the number of work submitted since the Pool has been created
     */
    public function submitted()
    {
        return $this->submitted;
    }

    /**
     * @return Worker[] Return the list of workers in the stack. Collected workers are no longer in this stack.
     */
    public function getWorks() : array
    {
        return $this->workers;
    }

    /**
     * @return int Return the number of work collected for garbage since the Pool has been created
     */
    public function collected()
    {
        return $this->collected;
    }

    public function processResults(\Closure $closure)
    {
        $found = 0;
        $resultBag = $this->getResultBag();

        while ($found < $this->submitted()) {

            $workResult = $this->getResultBag()->synchronized(function() use(&$found, $resultBag) {
                while (!count($resultBag)) {
                    $resultBag->wait();
                }

                $found++;
                return $resultBag->shift();
            });

            $closure($workResult);
        }
    }
}
