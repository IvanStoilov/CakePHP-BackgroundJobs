<?php

/**
 * Class BackgroundJobsShell
 * @property Job $Job
 *
 */
class BackgroundWorkerShell extends AppShell {
	public $uses = array('BackgroundJobs.Job');

	public function getOptionParser() {
		return parent::getOptionParser()
			->description(__("Used for managing background workers."))
			->addSubcommand('start', array('help' => __('Starts a new worker process which will poll for queued jobs every <interval> seconds.')))
				->addOption('interval', array ('short' => 'i', 'help' => 'Polling interval in seconds.', 'default' => 10, 'boolean' => false))
			->addSubcommand('stop', array('help' => __('Stops one background worker.')))
			->addSubcommand('status',array('help' => __('Get info about the currently started workers.')))
			->addSubcommand('stopAll',array('help' => __('Stops all active workers.')));
	}

	public function runQueued() {
		$job = $this->Job->reserve();

		if (empty($job)) {
			// no queued jobs found
			return;
		}

		$this->run($job);

		$this->Job->markAsFinished($job['Job']['id']);
	}

	public function run($job) {
		try {
			$params = json_decode($job['Job']['args'], 1);

			$Task = $this->Tasks->load($job['Job']['task']);
			$Task->initialize();

			call_user_func_array(array($Task, 'execute'), $params);
		} catch (Exception $ex) {
			CakeLog::write('job', "Job failed with exception: {$ex->getMessage()} - " . json_encode($job['Job']));
		}
	}

	public function rerun() {
		$jobId = $this->args[0];
		$job = $this->Job->findById($jobId);
		$this->run($job);
	}

	public function start() {
		$cmd = "app/Plugin/BackgroundJobs/Vendor/bgjobs.sh";
		$logFile = "app/tmp/logs/bgjobs.log";

		$interval = $this->params['interval'];

		exec(sprintf("%s %s > /dev/null 2> %s &", $cmd, $interval, $logFile), $output, $result);

		if ($result === 0) {
			$this->out("<info>Worker started (polling interval = {$interval}s)</info>\n");
		} else {
			$this->out("<error>Worker failed to start :(</error>");
		}
	}

	public function stop() {
		$pids = $this->_getWorkersPids();

		if (empty($pids)) {
			$this->out("Currently, no workers are running.\n");
			return false;
		}

		$workerToStop = $pids[0];

		$this->out("Stopping worker (pid=$workerToStop) ... ", false);

		$success = posix_kill($workerToStop, SIGTERM);

		if ($success) {
			$this->out("stopped.");
		} else {
			$this->out("<error>failed</error>.");
		}

		$this->out();
		return true;
	}

	public function stopAll() {
		do  {
			$thereAreMore = $this->stop();
		} while ($thereAreMore);
	}

	public function status()
	{
		$workers = $this->_getWorkersPids();
		if (!empty($workers)) {
			$this->out("<info>Currently started works: ".count($workers)."</info>\n");

			foreach ($workers as $worker) {
				$this->out("Worker (pid=$worker) is up.");
			}
		} else {
			$this->out("Currently, no workers are running.");
		}

		$this->out();
	}

	private function _getWorkersPids() {
		$cmd = "ps -o pid,comm,args | grep bgjobs | grep -v grep";

		exec($cmd, $output, $result);

		$workers = array();
		foreach ($output as $line) {
			$worker = explode(' ', trim($line));
			$workers[] = trim($worker[0]);
		}

		return $workers;
	}
}