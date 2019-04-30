<?php

namespace Ximdex\Workflow;

use Ximdex\Models\Node;

abstract class WorkflowAction
{
    protected $error;
    protected $node;
    
    public function __construct(Node $node)
    {
        $this->node = $node;
    }
    
    public function _getError()
    {
        return $this->error;
    }
}