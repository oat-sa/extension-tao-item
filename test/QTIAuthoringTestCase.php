<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
require_once dirname(__FILE__) . '/../../tao/lib/htmlpurifier/HTMLPurifier.standalone.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIAuthoringTestCase extends UnitTestCase {
	
	/**
	 * tests initialization
	 * load qti service
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * test the building of item from all the samples
	 */
	public function testSamples(){
		
		//check if samples are loaded 
		foreach(glob(dirname(__FILE__).'/samples/*.xml') as $file){	

			$qtiParser = new taoItems_models_classes_QTI_Parser($file);
			$item = $qtiParser->load();
			
			$this->assertTrue($qtiParser->isValid());
			$this->assertNotNull($item);
			$this->assertIsA($item, 'taoItems_models_classes_QTI_Item');
			
			foreach($item->getInteractions() as $interaction){
				$this->assertIsA($interaction, 'taoItems_models_classes_QTI_Interaction');
				
				// ensure the order of all choices supporting it can be restored
				$this->assertIsA(taoItems_models_classes_QtiAuthoringService::singleton()->getInteractionChoices($interaction), 'array');
				/*foreach ( as $choice) {
					$this->assertIsA($choice, 'taoItems_models_classes_QTI_Choice', 'Got non choice('.gettype($choice).') for item '.basename($file));
				}*/
			}
		}
	}
	
	public function log($msg){
		common_Logger::d('*********************************', array('QTIdebug'));
		common_Logger::d($msg, array('QTIdebug'));
	}
	
	public function _testEncodedData(){
		
		//html editor to QTI XML :
		
		
		//XML to html editor
		$prompt = '<div align="left"><img alt="Earth" src="img/earth.png"/><br/>
		&#xA0; Earth9</div><div align="left"><br/></div><div align="left"><div align="left">&lt;choiceInteraction shuffle="false"
		maxChoices="1"
		responseIdentifier="RESPONSE"&gt;&lt;prompt&gt;&lt;div
		align="left"&gt;&lt;img alt="Earth"
		src="img/earth.png"/&gt;&lt;br/&gt;</div><div align="left">&#xA0;
		Earth9&lt;/div&gt;&lt;/prompt&gt;&lt;/choiceInteraction&gt;</div></div>

		<mytag>yeah<mytag>';
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = taoItems_models_classes_QtiAuthoringService::filteredData($prompt);
		$this->log($prompt);
		
		$prompt = taoItems_models_classes_QtiAuthoringService::filteredData($prompt);
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = tao_helpers_Display::htmlToXml($prompt);//setPrompt(), setData();
		$this->log($prompt);
		
		$prompt = taoItems_models_classes_QtiAuthoringService::filteredData($prompt);
		$this->log($prompt);
		
		$prompt = taoItems_models_classes_QtiAuthoringService::filteredData($prompt);
		$this->log($prompt);
		
		$prompt = _dh($prompt);
		$this->log($prompt);
		
	}
	
	public function testFilterInvalidQTIhtml(){
		
		$qtiTags = array(
			'abbr',
			'acronym',
			'address',
			'blockquote',
			'br',
			'cite',
			'code',
			'dfn',
			'div',
			'em',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'kbd',
			'p',
			'pre',//not include img, object, big, small,sub, sup
			'q',
			'samp',
			'span',
			'strong',
			'var',
			'dl',
			'dt',
			'dd',
			'ol',
			'ul',
			'li',
			'object',//attributes(objectFlow, data, type, width, height)
			'param',//attributes(name,value,valuetype,type)
			'b',
			'big',
			'hr',
			'i',
			'small',
			'sub',
			'sup',
			'tt',
			'caption',
			'col',
			'colgroup',
			'table',//attributes(summary, caption, col, colgroup, thead, tfoot, tbody)
			'tablecell',
			'th',
			'td',
			'tbody',
			'tfoot',
			'thead',
			'tr',
			'img',//attr
			'a',
		);
		
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.AllowedElements', implode(',', $qtiTags));
		$purifier = new HTMLPurifier($config);
		
		$raw_html = '<div align="left"><img alt="Earth" src="img/earth.png"/><br/>
			&#xA0; Earth9</div><div align="left"><br/></div><div align="left"><div align="left">&lt;choiceInteraction shuffle="false"
			maxChoices="1"
			responseIdentifier="RESPONSE"&gt;&lt;prompt&gt;&lt;div
			align="left"&gt;&lt;img alt="Earth"
			src="img/earth.png"/&gt;&lt;br/&gt;</div><div align="left">&#xA0;
			Earth9&lt;/div&gt;&lt;/prompt&gt;&lt;/choiceInteraction&gt;</div></div>
			
			<div>
			<div>
			<p>&nbsp;<br></p>
			<div>

			<h1 class="western">A. Big features</h1>
			<h2 class="western">Missing interactions</h2>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font><font color="#444444">&nbsp;</font><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">graphicGapMatchInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">runtime
			buggy !! : </font></font></font></code>
			</p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">positionObjectInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">if
			we consider the positionObjectStage as the interaction and
			positionObjectInteraction as its "choice" then we can
			manage it quickly by adding some crappy "if
			positionObjectInteraction then …"</font></font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">mediaInteraction
			: new in QTI 2.1</font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">drawingInteraction
			: ???</font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">uploadInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">Not
			working on runtime : may not be so hard to be implemented, provided
			the way it should be stored in the result extension is clearly
			defined</font></font></font></code></p>
			<p align="LEFT" style="margin-bottom: 0cm; line-height: 0.5cm;">
			<code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt"><b>Class</b></font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code><code class="western"><font color="#444444"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">:&nbsp;</font></font></font></code><code class="western"><font color="#444444"><font size="2" style="font-size: 9pt">endAttemptInteraction
			: </font></font></code><code class="western"><font color="#000000"><font face="Helvetica, Arial, sans-serif"><font size="2" style="font-size: 9pt">In
			an adaptative item, need to bind this interaction to trigger response
			processing (corresponding to an end of attempt)</font></font></font></code><code class="western"><font color="#000000"><font size="2" style="font-size: 9pt">&nbsp;</font></font></code></p>
			<h2 class="western">Missing attributes</h2>
			<p>Loads...</p>
			<h2 class="western"><br></h2></div><br>

				b<br>ee
			<aside>dsdsd</aside>
			<div bla=\'aaa\' id=\'aaa\' title="pas cool">yeah
			<mytag>yeah<mytag>';
		$this->log($raw_html);
		
		$html = $purifier->purify($raw_html);
		$this->log($html);
	}
}
?>