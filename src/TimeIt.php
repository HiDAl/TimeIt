<?php

namespace HiDAl\TimeIt;

use HiDAl\TimeIt\Exception;

/**
*  A Simple Time Measure
*
*  This library focus on measure time, just wrapping microtime
*
*  @author Pablo Alcantar Morales <hidal@radiohead.cl>
*/
class TimeIt {
  /**  @var int $startTime Moment when time started */
  private $startTime;
  /**  @var int $lastInstant Last time when `elapsed` was called */
  private $lastInstant;
  /**  @var array $interval array of moments */
  private $instants;

  /**
  * Constructor
  *
  * @param boolean $autostart A boolean indicating if the time should auto-start
  *
  * @return void
  */
  public function __construct($autostart = true) {
    $this->startTime   = 0;
    $this->lastInstant = 0;
    $this->instants    = array();

    if ($autostart) {
      $this->start();
    }
  }

  /**
  * Start the timer
  *
  * @return float instant when timer started
  */
  public function start() {
    $this->startTime = $this->lastInstant = $this->current_time();

    return $this->startTime;
  }

  /**
  * Check if timer has started
  *
  * @return boolean
  */
  public function started() {
    return $this->startTime != 0;
  }

  /**
  * Mark a tick. Whenever you need know how much time elapsed.
  *
  * @param bool $relative If true the measure is relative to the last elapsed call, otherwise when timer started?
  *
  * @return float Time elapsed (relative or absolute)
  */
  public function elapsed($relative = true) {
    if ($this->startTime == 0) {
      throw new Exception("TimeIt must be started!", 1);
    }

    if ($relative) {
      $elapsed = $this->current_time() - $this->lastInstant;

    } else {
      $elapsed = $this->current_time() - $this->startTime;
    }

    $this->lastInstant = $this->current_time();

    $this->instants[] = $this->lastInstant;

    return $elapsed * 1000;
  }

  /**
  * Return the total time elapsed until this instant
  *
  * @return float Elapsed Time
  *
  */
  public function total() {
    return ($this->current_time() - $this->startTime) * 1000;
  }

  /**
  * Stop and restart the timer.
  *
  * @return float Absolute elapsed time.
  *
  */
  public function stop() {
    $elapsed = $this->total();

    $this->startTime = 0;

    return $elapsed;
  }

  /**
  * Get all ticks marked when elapsed was call
  *
  * @return array
  *
  */
  public function instants() {
    return $this->instants;
  }


  public function current_time() {
    return microtime(true);
  }

  /**
  * Static method for measure functions/method/block of codes
  *
  * ```
  * list($took, $return) = TimeIt::plz(function() {
  *   sleep(10);
  *
  *   return 10;
  * })
  *
  * echo $took . PHP_EOL;
  * echo $return . PHP_EOL;
  * ```
  *
  */
  public static function plz($callback, $arguments = array()) {
    if (!is_callable($callback)) {
      throw new Exception("HEY! How do you want I measure the time man? The callback is not callable!", 1);
    }

    $timeit = new TimeIt;
    $return = call_user_func_array($callback, $arguments);

    return array($timeit->stop(), $return);
  }
}
