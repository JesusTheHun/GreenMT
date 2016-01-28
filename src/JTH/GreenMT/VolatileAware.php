<?php

namespace JTH\GreenMT;

interface VolatileAware {
    public function setVolatile(\Volatile $pipe);
}