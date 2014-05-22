CakePHP-BackgroundJobs
======================

A lightweight plugin allowing you to queue jobs for delayed execution which requires no additional dependencies to be installed.

Instal
======

1. Go to your app plugin dir - cd app/Plugin

2. Get the source either by cloning or by adding it as a submodule
  git clone https://github.com/IvanStoilov/CakePHP-BackgroundJobs.git BackgroundJobs
  or
  git submodule add https://github.com/IvanStoilov/CakePHP-BackgroundJobs.git BackgroundJobs
  
3. Make sure the code is in the folder app/Plugin/BackgroundJobs

4. Load the plugin in your bootstrap.php
  Add this line at the end of Config/bootstrap.php
  
  CakePlugin::load('BackgroundJobs');

5. Create the jobs table - execute ./cake schema create --plugin BackgroundJobs
  
6. Done!

Usage
=====

Starting workers:

To start a worker that would run the background jobs you can use the shell script that goes with the plugin:

./cake BackgroundJobs.BackgroundWorker start

for more commands use ./cake BackgroundJobs.BackgroundWorker --help

Queueing Jobs
=============

You need to load the Job model to be able to queue jobs:

public $uses = array('Job' => array('classname' => 'BackgroundJobs.Job'));

then you can use that to queue job Tasks:
$this->Job->queue('SendMail, array('from@example.com', 'to@example.com', 'Hi!'));

This code will queue the following class for execution - Console/Command/Task/SendEmailsTask.php. Actually, the execute method will be called with the three arguments in the second argument.

Not finished .... TODO: write a better explanation :)
