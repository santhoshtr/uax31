<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('UAX31.php');

class TestOfUAX31 extends UnitTestCase {
	function testSpecialCharacters() {
		$this->assertFalse(isValidUserName('?തോട്ടിങ്ങല്‍'));
	}
	function testZWJMalayalam() {
		$this->assertTrue(isValidUserName('തോട്ടിങ്ങല്‍'));
	}
	function testZWJSinhala() {
		$this->assertTrue(isValidUserName('නන්දිමිත්‍ර'));
		$this->assertTrue(isValidUserName('සසීන්ද්‍ර'));
	}
}

$test = new GroupTest('UAX 31 test');
$test->addTestCase(new TestOfUAX31());
$test->run(new TextReporter());
?>