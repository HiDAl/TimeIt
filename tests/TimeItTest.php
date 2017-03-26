<?php

use HiDAl\TimeIt\TimeIt;

class TimeItTest extends \PHPUnit_Framework_TestCase {

  public function testTimerShouldStartAutomatically(){
    $time = new TimeIt;

    $this->assertTrue($time->started());
    $this->assertGreaterThan(0, $time->start());
  }

  public function testTimerNotStartAutomatically(){
    $time = new TimeIt(false);

    $this->assertFalse($time->started());
  }

  /**
   * @expectedException HiDAl\TimeIt\Exception
   */
  public function testTimeItShouldBeStartedBeforeCallElapsed() {
    $time = new TimeIt(false);

    // $this->expectException($time->elapsed());
    $this->setExpectedException(Exception::class);
    $time->elapsed();
  }

  public function testElapsedTimeRelative() {
    $time = $this->constructMockTimeIt();

    // Sleep 1 second
    $elapsedTime = $time->elapsed();

    $this->assertGreaterThanOrEqual(1000, $elapsedTime);
    $this->assertLessThanOrEqual(2000, $elapsedTime);

    // Sleep 1 second more
    $elapsedTime = $time->elapsed();

    $this->assertGreaterThanOrEqual(1000, $elapsedTime);
    $this->assertLessThanOrEqual(1500, $elapsedTime);
  }

  public function testElapsedTimeAbsolute() {
    $time = $this->constructMockTimeIt();

    // Sleep 1 second
    $elapsedTime = $time->elapsed();

    $this->assertGreaterThanOrEqual(1000, $elapsedTime);
    $this->assertLessThanOrEqual(2000, $elapsedTime);

    // Sleep 1 second more
    $elapsedTime = $time->elapsed(false);

    $this->assertGreaterThanOrEqual(2000, $elapsedTime);
    $this->assertLessThanOrEqual(3000, $elapsedTime);
  }

  public function testTotalElapsedTime() {
    $time = $this->constructMockTimeIt();

    $total = $time->total();

    $this->assertGreaterThanOrEqual(1000, $total);
    $this->assertLessThanOrEqual(2000, $total);
  }

  public function testStopTime() {
    $time = $this->constructMockTimeIt();

    $elapsed = $time->stop();

    $this->assertGreaterThanOrEqual(1000, $elapsed);
    $this->assertLessThanOrEqual(2000, $elapsed);
  }

  /**
   * @expectedException HiDAl\TimeIt\Exception
   */
  public function testStartThrowExceptionIfStopped() {
    $time = new TimeIt;

    $elapsed = $time->stop();

    // $this->expectException($time->elapsed());
    $this->setExpectedException(Exception::class);
    $time->elapsed();
  }

  public function testInstanstEqualZero() {
    $time  = new TimeIt;

    $instants = $time->instants();

    $this->assertEquals(0, count($instants));
  }

  public function testInstanstMoreThanOne() {
    $time  = $this->constructMockTimeIt();
    $times = 4;

    for ($i = 0; $i < $times; $i++) {
      $time->elapsed();
    }

    $instants = $time->instants();

    $this->assertEquals($times, count($instants));
  }

  /**
   * @expectedException HiDAl\TimeIt\Exception
   */
  public function testPlzBadArguments() {
    // $this->expectException(TimeIt::plz(null));
    $this->setExpectedException(Exception::class);
    TimeIt::plz(null);
  }

  public function testPlzWaitOneSecondAndReturnTrue() {
    $lambda = function() {
      sleep(1);
      return true;
    };

    $return = TimeIt::plz($lambda);

    $this->assertInternalType('array', $return);

    list($elapsed, $returnValue) = $return;

    $this->assertGreaterThanOrEqual(1000, $elapsed);
    $this->assertLessThanOrEqual(2000, $elapsed);

    $this->assertTrue($returnValue);
  }

  // Mock Object for simulate 1 seconds of sleep between calls
  private function constructMockTimeIt() {
    $classname = 'HiDAl\TimeIt\TimeIt';

    $mock = $this->getMockBuilder($classname)
      ->disableOriginalConstructor()
      ->setMethods(['current_time'])
      ->getMock();

    $mock
      ->expects($this->any())
      ->method("current_time")
      ->will($this->onConsecutiveCalls(1, 2, 3, 4));

    // now call the constructor
    $reflectedClass = new ReflectionClass($classname);
    $constructor = $reflectedClass->getConstructor();
    $constructor->invoke($mock);

    return $mock;
  }
}
