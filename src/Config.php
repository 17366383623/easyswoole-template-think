<?php

namespace EasySwoole\Template\Think;


class Config
{
    /** @var int timeout */
    private $timeout = 0.5;

    /** @var int workNum */
    private $workerNum = 3;

    /** @var string renderProcessNamePrefix */
    private $socketPrefix = 'render';

    /**
     * get timeout
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return (float)$this->timeout;
    }

    /**
     * get render worker process number
     *
     * @return int
     */
    public function getWorkerNum(): int
    {
        return $this->workerNum;
    }

    /**
     * get the process name prefix of render
     *
     * @return string
     */
    public function getSocketPrefix(): string
    {
        return $this->socketPrefix;
    }

    /**
     * set timeout
     *
     * @param float $timeout
     */
    public function setTimeout(float $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * set worker number
     *
     * @param int $num
     */
    public function setWorkerNum(int $num): void
    {
        $this->workerNum = $num;
    }

    /**
     * set the process's prefix of render
     *
     * @param string $prefix
     */
    public function setSocketPrefix(string $prefix): void
    {
        $this->socketPrefix = $prefix;
    }
}