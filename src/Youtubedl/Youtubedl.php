<?php

namespace Youtubedl;

use Symfony\Component\Process\Process;
use Youtubedl\Exceptions\YoutubedlException;

class Youtubedl
{
    private $async = false;
    private $verbose = false;
    private $option;
    private $link;

    public function __construct()
    {
        $this->option = new Option();
    }

    public function isAsync($bool = false)
    {
        $this->async = $bool;

        return $this;
    }

    public function isVerbose($bool = false)
    {
        $this->verbose = $bool;

        return $this;
    }

    public function getOption()
    {
        return $this->option;
    }

    public function download($link)
    {
        if (is_array($link)) {
            $link = implode(' ', $link);
        }
        $this->link = $link;

        return $this;
    }

    public function execute()
    {
        $process = new Process(Config::getBinFile()." {$this->option} -- {$this->link}");
        if ($this->verbose) {
            $process->run(function($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });
        } else {
            ($this->async) ? $process->start() : $process->run();
        }
        if (!$process->isSuccessful()) {
            throw new YoutubedlException($process->getErrorOutput());
        }

        return explode("\n", trim($process->getOutput()));
    }
}
