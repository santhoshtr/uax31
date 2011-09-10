<?php
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('UAX31.php');

class TestOfUAX31 extends UnitTestCase {
	function testSpecialCharacters() {
		$this->assertFalse(isIdentifier('?തോട്ടിങ്ങല്‍'));
		$this->assertFalse(isIdentifier(' abcd'));
		$this->assertFalse(isIdentifier('.abcd'));
	}
	function testZWJMalayalam() {
		$this->assertTrue(isIdentifier('തോട്ടിങ്ങല്‍'));
	}
	function testZWJSinhala() {
		$this->assertTrue(isIdentifier('නන්දිමිත්‍ර'));
		$this->assertTrue(isIdentifier('සසීන්ද්‍ර'));
	}
	function testMixedScript() {
		$this->assertFalse(isIdentifier('abcdതോട്ടിങ്ങല്‍'));
	}
}

$test = new GroupTest('UAX 31 test');
$test->addTestCase(new TestOfUAX31());
$test->run(new TextReporter());
?>