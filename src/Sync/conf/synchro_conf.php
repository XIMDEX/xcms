<?php

/**
 *  \details &copy; 2018 Open Ximdex Evolution SL [http://www.ximdex.org]
 *
 *  Ximdex a Semantic Content Management System (CMS)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  See the Affero GNU General Public License for more details.
 *  You should have received a copy of the Affero GNU General Public License
 *  version 3 along with Ximdex (see LICENSE file).
 *
 *  If not, visit http://gnu.org/licenses/agpl-3.0.html.
 *
 *  @author Ximdex DevTeam <dev@ximdex.com>
 *  @version $Revision$
 */

// Paths and directories related to the Synchronizer
if (! defined('SERVERFRAMES_SYNC_PATH')) {
	define('SERVERFRAMES_SYNC_PATH', XIMDEX_ROOT_PATH . '/data/sync/serverframes');
}
if (! defined('PUMPERPHP_PATH')) {
	define('PUMPERPHP_PATH', XIMDEX_ROOT_PATH . '/src/Sync/scripts/pumper/php');
}

// Scheduler & Pumper
if (! defined('SCHEDULER_CHUNK')) {
	define('SCHEDULER_CHUNK', 10);
}

// Max uploading time for a single file (10 minutes)
if (! defined('MAX_CHECK_TIME_FOR_PUMPER')) {
	define('MAX_CHECK_TIME_FOR_PUMPER', 600);
}

// Max starting time for a pumper process in seconds
if (! defined('MAX_STARTING_TIME_FOR_PUMPER')) {
    define('MAX_STARTING_TIME_FOR_PUMPER', 60);
}

// Number of server frames to process per each running pumper. Cero value to unlimited task
if (! defined('MAX_TASKS_PER_PUMPER')) {
    define('MAX_TASKS_PER_PUMPER', 0);
}

// Default batch priority
if (! defined('DEFAULT_BATCH_PRIORITY')) {
    define('DEFAULT_BATCH_PRIORITY', 0.5);
}

// Maximum number of scheduler cycles
if (! defined('MAX_NUM_CICLOS_SCHEDULER')) {
	define('MAX_NUM_CICLOS_SCHEDULER', 1000);
}

// Maximum number of empty scheduler cycles
if (! defined('MAX_NUM_CICLOS_VACIOS_SCHEDULER')) {
	define('MAX_NUM_CICLOS_VACIOS_SCHEDULER', 2880);
}

// Time in second that scheduler sleeps for each empty cycle
if (! defined('SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE')) {
	define('SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE', 15);
}

// Maximum number of batch cycles multiplicator
if (! defined('MAX_NUM_CICLOS_BATCH')) {
	define('MAX_NUM_CICLOS_BATCH', 100);
}

// Type of script used to pumper (pl|php)
if (! defined('PUMPER_SCRIPT_MODE')) {
	define('PUMPER_SCRIPT_MODE', 'php');
}

// Maximun number of nodes per batch...
if (! defined('MAX_NUM_NODES_PER_BATCH')) {
	define('MAX_NUM_NODES_PER_BATCH', 20);
}

// Deep level for resolving dependencies, 0 to N, recommended 1
if (! defined('DEEP_LEVEL')) {
	define('DEEP_LEVEL', 1);
}

// Force the publication of documents already published. TRUE or FALSE, by default FALSE
if (! defined('FORCE_PUBLICATION')) {
	define('FORCE_PUBLICATION', false);
}

// Number of scheduler batch publication cycles to wait in order to print publish stats in log 
if (! defined('CYCLES_BETWEEN_SHOW_STATS')) {
    define('CYCLES_BETWEEN_SHOW_STATS', 50);
}
