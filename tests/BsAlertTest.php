<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class BsAlertTest extends TestCase
{
  public function testAlert(): void
  {
    $message = "This is a test.";
    $this->expectOutputString("<div class='alert alert-info center-block'><p class='text-center'>This is a test.</p></div>");
    $alert = bs_alert($message, 'info');

  }
}
