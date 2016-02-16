<?php

namespace Ximdex;
/**
 * Class Events
 * Stores all the event names
 *
 * @package Ximdex
 */
final class Events
{
    /**
     * The ximdex.start event is thrown each time Ximdex starts
     *
     * @var string
     */
    const XIMDEX_START = 'ximdex.start';

    /**
     * The node.touched event is thrown each time a node is modified
     *
     * @var string
     */
    const NODE_TOUCHED = 'node.touched';
}