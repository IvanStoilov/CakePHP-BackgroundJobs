<?php

App::uses('AppModel', 'Model');

class Job extends AppModel {
	const STATUS_QUEUED = 'QUEUED';
	const STATUS_FINISHED = 'FINISHED';

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

	public function getQueuedJobs($limit = 1) {
		return $this->find('all', array(
			'conditions' => array (
				'status' => self::STATUS_QUEUED,
			),
			'limit' => $limit,
			'order' => 'id'
		));
	}
}