<?php

App::uses('AppModel', 'Model');

class Job extends AppModel {
	const STATUS_QUEUED = 'QUEUED';
	const STATUS_FINISHED = 'FINISHED';
	const STATUS_INPROGRESS = 'INPROGRESS';

	public function queue($task, $args)
	{
		$this->create();
		return $this->save(array(
			'task' => $task,
			'args' => json_encode($args)
		));
	}

	public function markAsFinished($jobId) {
		return $this->save(array(
			'id' => $jobId,
			'status' => self::STATUS_FINISHED
		));
	}

	public function markAsInProgress($jobId) {
		return $this->save(array(
			'id' => $jobId,
			'status' => self::STATUS_INPROGRESS
		));
	}

	public function reserve() {
		$job = $this->findByStatus(self::STATUS_QUEUED);

		if (empty($job)) {
			return false;
		}

		$this->markAsInProgress($job['Job']['id']);

		// check for race conditions
		if ($this->getAffectedRows() === 0) {
			return false;
		}

		return $job;
	}
}