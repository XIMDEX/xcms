<?php

/**
 *  \details &copy; 2011  Open Ximdex Evolution SL [http://www.ximdex.org]
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


/*
 *	Paths and directories related to the Synchronizer.
*/

if (!defined('CHANNELFRAMES_SYNC_PATH'))
	define('CHANNELFRAMES_SYNC_PATH', XIMDEX_ROOT_PATH."/data/sync/channelframes");

if (!defined('SERVERFRAMES_SYNC_PATH'))
	define('SERVERFRAMES_SYNC_PATH', XIMDEX_ROOT_PATH."/data/sync/serverframes");


if (!defined('PUMPERPHP_PATH'))
	define('PUMPERPHP_PATH', XIMDEX_ROOT_PATH.\Ximdex\Modules\Manager::path('ximSYNC')."/scripts/pumper/php");

/*
 *	Paging the monitor panel of batch publication
*/

if (!defined('MANAGEBATCHS_BATCHS_PER_PAGE'))
	define('MANAGEBATCHS_BATCHS_PER_PAGE', 25);

if (!defined('MANAGEBATCHS_FRAMES_PER_PAGE'))
	define('MANAGEBATCHS_FRAMES_PER_PAGE', 25);

/*
 *	Scheduler & Pumper
*/

if (!defined('SCHEDULER_CHUNK'))
	define('SCHEDULER_CHUNK', 10);

if (!defined('UNACTIVITY_CYCLES'))
	define('UNACTIVITY_CYCLES', 5);

if (!defined('MAX_CHECK_TIME_FOR_PUMPER'))
	define('MAX_CHECK_TIME_FOR_PUMPER', 1800); // max uploading time for a single file (30 minutes)

// Minimum batch system priority...
if (!defined('MIN_SYSTEM_PRIORITY'))
	define('MIN_SYSTEM_PRIORITY', 0.2);

// Maximum batch system priority...
if (!defined('MAX_SYSTEM_PRIORITY'))
	define('MAX_SYSTEM_PRIORITY', 0.5);

// Minimum batch total priority...
if (!defined('MIN_TOTAL_PRIORITY'))
	define('MIN_TOTAL_PRIORITY', 0.1);

// Maximum batch total priority...
if (!defined('MAX_TOTAL_PRIORITY'))
	define('MAX_TOTAL_PRIORITY', 1);

// Maximum number of scheduler cycles...
if (!defined('MAX_NUM_CICLOS_SCHEDULER'))
	define('MAX_NUM_CICLOS_SCHEDULER', 1000);

// Maximum number of empty scheduler cycles...
if (!defined('MAX_NUM_CICLOS_VACIOS_SCHEDULER'))
	define('MAX_NUM_CICLOS_VACIOS_SCHEDULER', 2880);

// Time in second that scheduler sleeps for each empty cycle...
if (!defined('SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE'))
	define('SCHEDULER_SLEEPING_TIME_BY_VOID_CYCLE', 15);

// Maximum number of batch cycles...
if (!defined('MAX_NUM_CICLOS_BATCH'))
	define('MAX_NUM_CICLOS_BATCH', 10);

// Type of script used to pumper (pl|php)
if (!defined('PUMPER_SCRIPT_MODE'))
	define('PUMPER_SCRIPT_MODE', 'php');

// Maximun number of nodes per batch...
if (!defined('MAX_NUM_NODES_PER_BATCH'))
	define('MAX_NUM_NODES_PER_BATCH', 20);

// Deep level for resolving dependencies, 0 to N, recomended 1.
if (!defined('DEEP_LEVEL'))
	define('DEEP_LEVEL', 1);

// Force the publication of documents already published. TRUE or FALSE, by default FALSE
if (!defined('FORCE_PUBLICATION'))
	define('FORCE_PUBLICATION', false);
