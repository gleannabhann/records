<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ApologizeTest extends TestCase
{
  public function testApologize(): void
  {
    $message = "This is a test";
    $end_message = '<p class="text-danger">'. htmlspecialchars($message).'</p>';
    //ob_start();
    apologize($message);
    //$html = ob_get_flush();
    //$this->assertStringContainsString($end_message, $html);
  }
}
