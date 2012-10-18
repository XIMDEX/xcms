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




if (!defined('XIMDEX_ROOT_PATH'))
	define('XIMDEX_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));

if (!defined('POOLER_ROOT_PATH'))
	define('POOLER_ROOT_PATH', realpath(dirname(__FILE__)));

//require_once 'debug.php';
require_once POOLER_ROOT_PATH . '/PIDFile.class.php';
require_once POOLER_ROOT_PATH . '/PoolerConf.class.php';
require_once POOLER_ROOT_PATH . '/ServerSocket.class.php';
//require_once POOLER_ROOT_PATH . '/ProcessQueue.class.php';
require_once POOLER_ROOT_PATH . '/ClientSocket.class.php';
require_once POOLER_ROOT_PATH . '/Queue_Process.class.php';

require_once XIMDEX_ROOT_PATH . '/inc/MPM/SharedMemory.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/log/Log.class.php';
require_once XIMDEX_ROOT_PATH . '/inc/log/XMD_log.class.php';


declare(ticks = 1);

set_time_limit(0);
ob_implicit_flush();


$children = array();

/**
 *
 */
function main($argc, $argv) {

	$confFile = XIMDEX_ROOT_PATH . '/conf/poolerd.xml';
	$conf = null;
	$PIDFile = null;
	$commands = array('start', 'stop', 'forcestop', 'restart', 'status');
	$command = '';
	$debug = false;

	if ($argc < 2) {
		usage($argv, $commands);
		exit(1);
	}

	for ($c=1; $c<count($argv); $c++) {

		$arg = $argv[$c];
		switch ($arg) {
			case '-c':
				$confFile = $argv[++$c];
				break;
			case '-k':
				$cmd = $argv[++$c];
				if (!in_array($cmd, $commands)) {
					echo "\n- Command '$cmd' not valid\n";
					usage($argv, $commands);
					exit(1);
				}
				if (!function_exists($cmd)) {
					echo "- Can't find command $cmd!\n";
					exit(1);
				}
				break;
			default:
				echo "\n- Option $arg not recognized\n";
				usage($argv, $commands);
				exit(1);
		}
	}

	try {

		$conf = new PoolerConf($confFile);
		$conf->server['debug'] = $debug;
		$conf->server['pid'] = new PIDFile($conf->server['pidfile']);
		$conf->server['sharedMemory'] = createSharedMemory();

	} catch(Exception $e) {
		printf("- Error parsing configuration file: %s\n", $e->getMessage());
		exit(1);
	}

	try {
		$ret = $cmd($conf);
		exit($ret);
	} catch(Exception $e) {
		printf("- Error executing command $cmd: %s\n", $e->getMessage());
		$conf->server['pid']->delete();
		$conf->server['sharedMemory']->destroyMemory();
		exit(1);
	}

}

function & createSharedMemory() {
	$SHM_KEY = ftok(__FILE__, chr(1));
	$shared = new SharedMemory($SHM_KEY, 1000000 /*bytes*/);
	$shared->create();
	return $shared;
}

/**
 * Sets the user and group of the running process
 */
function changeUserId($conf) {

	$user = $conf->server['user'];
	$group = $conf->server['group'];

	$userinfo = posix_getpwnam($user);
	if (false === $userinfo) {
		throw new Exception("User $user doesn't exists.");
	}

	$groupinfo = posix_getgrnam($group);
	if (false === $groupinfo) {
		throw new Exception("Group $group doesn't exists.");
	}

	if (!posix_setgid($groupinfo['gid'])) {
		throw new Exception("Can't change identity to group $group");
	}

	if (!posix_setuid($userinfo['uid'])) {
		throw new Exception("Can't change identity to user $user");
	}

	$user_dir = $userinfo['dir'];
	if ($conf->server['userdir'] != '') $user_dir = $conf->server['userdir'];
	if (!is_dir($user_dir)) $user_dir = '/tmp';
	$ret = !chdir($user_dir);

	return (int) $ret;
}

/**
 * Creates a child process and makes it become session leader
 */
function daemonize($conf) {

	$child = pcntl_fork();

	if ($child == -1) {

		throw new Exception("Can't become a daemon.");
	} else if ($child > 0) {

		// Parent process
	} else {

		// Child process
		posix_setsid(); // become session leader
	    chdir('/tmp');
	    // Be sure to pass an integer to umask()
	    umask(octdec($conf->server['umask']));
	    $pid = posix_getpid();
	}

    return $child;
}

function & createQueueProcess($index, &$queues) {
	$queue = $queues[$index];
	$queue = new Queue_Process($queue);
	$queues[$index] =& $queue;
	return $queue;
}

function prefork($conf) {

	$children = array();

	for ($i=0; $i<count($conf->queues); $i++) {

		$child = pcntl_fork();
		if ($child == -1) {
			// Can't fork!
		} else if ($child == 0) {

			// Register signals for the child process
			pcntl_signal(SIGTERM, 'childSigHandler');
			pcntl_signal(SIGHUP,  'childSigHandler');

			$pid = posix_getpid();
			$queue =& createQueueProcess($i, $conf->queues);
			$queue->setPID($pid);
			$queue->setSharedMemory($conf->server['sharedMemory']);
			$queue->run();

			exit(0);

		} else {

			$children[$child] = $child;
			$queue =& createQueueProcess($i, $conf->queues);
			$queue->setPID($child);
			$queue->setSharedMemory($conf->server['sharedMemory']);
		}
	}

	$child = pcntl_fork();
	if ($child == -1) {
		// Can't fork!
	} else if ($child == 0) {

		// Register signals for the child process
		pcntl_signal(SIGTERM, 'childSigHandler');
		pcntl_signal(SIGHUP,  'childSigHandler');

		$serverProc = new ServerSocket($conf);
		$ret = $serverProc->handle();

		exit(0);

	} else {

		$children[$child] = $child;
	}

	return $children;
}

function daemonSigHandler($signal) {

	global $children;

	switch ($signal) {
		case SIGTERM:
			foreach ($children as $pid) {
				$ret = posix_kill($pid, SIGTERM);
			}
			exit(0);
			break;
		case SIGHUP:
			// handle restart tasks
			break;
		default:
			// handle all other signals
	}
}

function childSigHandler($signal) {

	global $children;

	switch ($signal) {
		case SIGTERM:
			$pid = posix_getpid();
			unset($children[$pid]);
			exit(0);
			break;
		case SIGHUP:
			// handle restart tasks
			break;
		default:
			// handle all other signals
	}
}

/**
 * Starts the daemon
 */
function start($conf) {

	// Children PIDs
	global $children;

	if ($conf->server['pid']->checkServer()) {
		echo "- Server already running.\n";
		return 1;
	}

	echo "- Starting server...";
	$pid = daemonize($conf);

	if ($pid > 0) {

		$count = 0;
		$checkPID = true;

		// Wait until child process is ready
		while (!$conf->server['pid']->checkServer() && $checkPID && $count < 10) {
			$count++;
			sleep(1);
			$checkPID = posix_kill($pid, 0);
			echo '.';
		}
		echo "\n";

		$ret = 0;
		if (!$conf->server['pid']->checkServer() || !$checkPID) {
			$ret = 1;
			echo "- Can't start the server.!\n";
			forcestop($conf);
		} else {
			echo "- Server is running.\n";
		}

		// When parent process exits, detached child becomes a daemon
		return $ret;

	} else {

		// TODO: Close descriptors? Send to /dev/null?
//		fclose(STDIN);
//		fclose(STDOUT);	// Fails to start the daemon!
//		fclose(STDERR);

		try {
			$ret = changeUserId($conf);
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
//			exit(1);
		}

		// Register signals for the daemon
		pcntl_signal(SIGTERM, 'daemonSigHandler');
		pcntl_signal(SIGHUP,  'daemonSigHandler');
//		pcntl_signal(SIGUSR1, 'daemonSigHandler');

		$conf->server['pid']->create(posix_getpid());
		$children = prefork($conf);
		// NOTE: Waith for children, but don't block the main process or we can't catch the signals!
		while (($pid = pcntl_waitpid(-1, &$status, WNOHANG)) != -1) {
			// Use sleep and so the CPU don't go to 100% ...
			// ... but maybe some zombies could be appear...
			sleep(1);
		}
		$conf->server['pid']->delete();
		$conf->server['sharedMemory']->destroyMemory();
	}

	return 0;
}

/**
 * Stops the daemon
 */
function stop($conf) {

	if (!$conf->server['pid']->checkServer()) {
		echo "- Server is not running.\n";
		return 0;
	}

	echo "- Stoping server...";
	$pid = $conf->server['pid']->getPID();
	$ret = posix_kill($pid, SIGTERM);

	$count = 0;
	while ($conf->server['pid']->checkServer() && $count < 10) {
		$count++;
		sleep(1);
		echo '.';
	}
	echo "\n";

	$ret = 0;
	if ($conf->server['pid']->checkServer()) {
		$ret = 1;
		echo "- Can't stop the server.!\n";
	} else {
		echo "- Server is stopped.\n";
		$conf->server['pid']->delete();
	}
	return $ret;
}

/**
 * Kills the daemon
 */
function forcestop($conf) {

	if (!$conf->server['pid']->checkServer()) {
		echo "- Server is not running.\n";
		return 0;
	}

	echo "- Killing server...\n";
	$pid = $conf->server['pid']->getPID();
	posix_kill($pid, SIGKILL);
	$conf->server['pid']->delete();
}

/**
 * Restart the daemon
 */
function restart($conf) {
//		echo "- Restarting server...\n";
	$ret = stop();
	if ($ret == 1) return 1;
	$ret = start();
	if ($ret == 1) return 1;
	return 0;
}

/**
 * Shows the daemon status
 */
function status($conf) {
	$status = (int) $conf->server['pid']->checkServer();
	if ($status != 0) {
		echo "- Server is running.\n";
	} else {
		echo "- Server is stoped.\n";
	}
	return $status;
}

/**
 * Command line usage
 */
function usage($argv, $commands) {
	$basename = basename($argv[0]);
	echo "\n- Usage: $basename -k [". implode(' | ', $commands) ."] (-c [config_path])\n";
	echo "- Without params checks config file and show this help.\n";
}


main($argc, $argv);

?>
