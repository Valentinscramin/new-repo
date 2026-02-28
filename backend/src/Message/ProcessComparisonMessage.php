<?php

namespace App\Message;

class ProcessComparisonMessage
{
    private int $comparisonId;

    public function __construct(int $comparisonId)
    {
        $this->comparisonId = $comparisonId;
    }

    public function getComparisonId(): int
    {
        return $this->comparisonId;
    }
}
