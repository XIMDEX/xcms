<?php

namespace Ximdex\Workflow\Actions;

use Ximdex\Models\Node;

abstract class WorkflowAction
{
    protected $error;
    protected $node;
    
    public function __construct(Node $node)
    {
        $this->node = $node;
    }
    
    public function getError()
    {
        return $this->error;
    }
}