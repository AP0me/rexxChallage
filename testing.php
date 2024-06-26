<?php

class MyTest {
  public function assertEquals($expected, $actual, $message = '') {
    if ($expected === $actual) {
      echo "PASS: " . $message . "\n";
    } else {
      echo "FAIL: " . $message . "\n";
      echo "Expected: " . var_export($expected, true) . "\n";
      echo "Actual: " . var_export($actual, true) . "\n";
    }
  }
}


