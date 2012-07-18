<?php
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';

//get the test into each extensions
$tests = TestRunner::getTests(array('taoItems'));

//create the test sutie
$testSuite = new TestSuite('TAO Item unit tests');
foreach($tests as $testCase){
	$testSuite->addFile($testCase);
}    

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}
require_once  PHPCOVERAGE_HOME. "/CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "/reporter/HtmlCoverageReporter.php";
//run the unit test suite
$includePaths = array(ROOT_PATH.'taoItems/models',ROOT_PATH.'taoItems/helpers');
$excludePaths = array();
$covReporter = new HtmlCoverageReporter("Code Coverage Report taoItems", "", PHPCOVERAGE_REPORTS."/taoItems");
$cov = new CoverageRecorder($includePaths, $excludePaths, $covReporter);
//run the unit test suite
$cov->startInstrumentation();
$testSuite->run($reporter);
$cov->stopInstrumentation();
$cov->generateReport();
$covReporter->printTextSummary(PHPCOVERAGE_REPORTS.'/taoItems_coverage.txt');
?>