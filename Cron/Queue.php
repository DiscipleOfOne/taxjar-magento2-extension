<?php

namespace Taxjar\SalesTax\Cron;

use Taxjar\SalesTax\Model\Queue as TjQueue;

class Queue {

    /**
     * @var TjQueue
     */
    protected $queue;

    public function __construct(TjQueue $queue) {
        $this->queue = $queue;
    }

    public function processQueue() {
        $this->queue->process();
    }
}
