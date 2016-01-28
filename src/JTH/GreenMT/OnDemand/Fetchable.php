<?php

namespace JTH\GreenMT\OnDemand;

interface Fetchable {
    /**
     * @return array|bool Must return an array or false if no more data is available
     */
    public function fetch();
}