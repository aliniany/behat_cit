<?php
#Pharmacists of Nurses who are specifically trained on all aspects of the Janssen products and the therapeutic areas they treat.
#bin/behat -dl --lang=en
#java -jar selenium-server-standalone-2.45.0.jar

use Behat\Behat\Context\ClosuredContextInterface,
  Behat\Behat\Context\TranslatedContextInterface,
  Behat\Behat\Context\BehatContext,
  Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
  Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;


/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
  public function __construct(array $parameters)
  {
    // Initialize your context here
  }

  /**
   * @When /^I hover over the element "([^"]*)"$/
   */
  public function iHoverOverTheElement($locator)
  {

    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->mouseOver();
  }

  /** Click on the element with the provided xpath query
   *
   * @When /^(?:|I )click on the element "([^"]*)"$/
   */
  public function iClickOnTheElement($locator)
  {
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

    // errors must not pass silently
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->click();
  }


  /** Hover the element to see a content
   *
   * @When /^(?:|I )hover the element "([^"]*)" and then the specific element "([^"]*)"$/
   */
  public function iHoverTheElementAndThenTheSpecificElement($locator2, $locator1)
  {
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator2); // runs the actual query and returns the element

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator2));
    }

    $element = $element->find('css', $locator1);
    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator1));
    }

    $element->mouseOver();
  }

  /** Focus the element
   *
   * @When /^(?:|I )focus the element "([^"]*)"$/
   */
  public function iFocusTheElement($locator)
  {
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $element->Focus();
  }

  /** Take a screenshot
   * @Then /^I want take a screenshot named  "([^"]*)"  in the folder  "([^"]*)"$/
   */
  public function iWantTakeAScreenshotNamedInTheFolder($filename, $folder)
  {
    if (is_dir($folder) == False) {
      throw new \InvalidArgumentException(sprintf('Folder: "%s" not exists', $folder));
    }
    $this->saveScreenshot($filename, $folder);

  }

  /** Wait some seconds before continue
   * @Then /^I wait (?P<num>\d+) seconds before continue$/
   */
  public function iWaitSecondsBeforeContinue($seconds)
  {
    #$this->getSession()->wait($seconds);
    sleep($seconds);
  }

  /** I should see a download link in the text
   * @Then /^I should see a download link in the text "([^"]*)"$/
   */
  public function iShouldSeeADownloadLinkInTheText($text)
  {
    $this->getSession()->getPage()->findLink($text);
  }

  /**
   * @Given /^I reset the session$/
   */
  public function iResetTheSession()
  {
    $this->getSession()->reset();
  }

  /**
   * Checks, that form element with is visible on page.
   *
   * @Then /^(?:|I )should see a visible "([^"]*)" element$/
   */
  public function assertElementOnPage($locator)
  {
    $element = $this->getSession()->getPage();
    $nodes = $element->find('css', $locator);

    if (null === $nodes) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    if ($nodes->isVisible()) {
      return;
    } else {
      throw new \Exception("The element \"$locator\" is not visible.");
    }
  }

  /**
   * Checks, that form element with is not visible on page.
   *
   * @Then /^(?:|I )should not see an invisible "([^"]*)" element$/
   */
  public function iShouldNotSeeAElement($locator)
  {
    $element = $this->getSession()->getPage();
    $nodes = $element->find('css', $locator);

    if (null === $nodes) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    if ($nodes->isVisible()==0) {
      return;
    } else {
      throw new \Exception("The element \"$locator\" is visible.");
    }
  }
  
  /**
   * @Given /^I set browser window size to "([^"]*)" x "([^"]*)"$/
   */
  public function iSetBrowserWindowSizeToX($width, $height) {
    $this->getSession()->resizeWindow((int)$width, (int)$height, 'current');
  }
  
  /**
	 * Scroll to a certain element by CSS selector.
	 * Requires an "class" attribute to uniquely identify the element in the document.
	 *
	 * Example: Given I scroll to the ".css_element" element
	 *
	 * @Given /^I scroll to the "(?P<locator>(?:[^"]|\\")*)" element$/
	 */
	public function iScrollToElement($locator) {
		$page = $this->getSession()->getPage();
        $el = $page->find('css', $locator);

    if ($el === null){
      //sprintf("element not found %s", $el);
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $class = $el->getAttribute('class');
		if(empty($class)) {
			throw new \InvalidArgumentException('Element requires an "id" attribute');
		}
		$js = sprintf("document.getElementsByClassName('%s')[0].scrollIntoView(true);", $class);
		$this->getSession()->executeScript($js);
	}
	
	/**
    * Click on a certain link by text found.
    *
    * @When /^I click on the element name "(?P<text>(?:[^"]|\\")*)"$/
    */
    public function iClickOnTheText($text)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', '*//*[text()="'. $text .'"]')
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }
 
        $element->click();
    }
	
	/**
	 * Scroll to a certain element by label.
	 * Requires an "id" attribute to uniquely identify the element in the document.
	 *
	 * Example: Given I scroll to the "Submit" button
	 * Example: Given I scroll to the "My Date" field
	 *
	 * @Given /^I scroll to the "([^"]*)" (field|link|button)$/
	 */
	public function iScrollToField($locator, $type) {
		$page = $this->getSession()->getPage();
    $el = $page->find('named', array($type, "'$locator'"));

    if ($el === null){
      //sprintf("element not found %s", $el);
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $id = $el->getAttribute('id');
		if(empty($id)) {
			throw new \InvalidArgumentException('Element requires an "id" attribute');
		}
		$js = sprintf("document.getElementById('%s').scrollIntoView(true);", $id);
		$this->getSession()->executeScript($js);
	}
	
	/**
	 * @Given /^I scroll to the top$/
	 */
	public function iScrollToTop() {
		$this->getSession()->executeScript('window.scrollTo(0,0);');
	}
	
	/**
	 * @Given /^I scroll to the bottom$/
	 */
	public function iScrollToBottom() {
		$javascript = 'window.scrollTo(0, Math.max(document.documentElement.scrollHeight, document.body.scrollHeight, document.documentElement.clientHeight));';
		$this->getSession()->executeScript($javascript);
	}

  /**
   * @Then /^the content of repeated "([^"]*)" div should contain "(?P<text>(?:[^"]|\\")*)"$/
   */
  public function iReadContentOfDiv($class, $text)
  {
    $session = $this->getSession();
    $page = $session->getPage();
    $element = $page->findAll('css', $class);

    if (null === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS: "%s"', $class));
    }

    foreach ($element as $e) {

      # Replace quotes to use strstr
      $text = str_replace('\"','',$text);
      $text_div = $e->getText();
      $text_div = str_replace('"',"",$text_div);

      if (strstr($text_div, $text)){
        return;
      }
    }

    throw new Exception(sprintf('Data "%s" not found in element "%s".', $text, $class));

  }
  
  /**
	 * Check if element is disabled.
	 * Requires an "class" attribute to uniquely identify the element in the document.
	 *
	 * Example: Then the ".css_element" element should be disabled
	 *
	 * @Then /^the "([^"]*)" element should be disabled$/
	 */
	public function elementIsDisabled($locator) {
		$page = $this->getSession()->getPage();
    $el = $page->find('css', $locator);

    if ($el === null){
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $disabled = $el->getAttribute('disabled');
		if(empty($disabled)) {
			throw new \InvalidArgumentException('Element is not disabled. Element requires an "disabled" attribute');
		}
	}
	
	/**
	 * Check if element is enabled.
	 * Requires an "class" attribute to uniquely identify the element in the document.
	 *
	 * Example: Then the ".css_element" element should be enabled
	 *
	 * @Then /^the "([^"]*)" element should be enabled$/
	 */
	public function elementIsEnabled($locator) {
		$page = $this->getSession()->getPage();
        $el = $page->find('css', $locator);

    if ($el === null){
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $disabled = $el->getAttribute('disabled');
		if(!empty($disabled)) {
			throw new \InvalidArgumentException('Element is disabled.');
		}
	}

  /**
   * Example: Then I should see 6 options on the "jnj_remember_me_question_list" select element
   *
   * @Then /^I should see (?P<num>\d+) options on the "([^"]*)" select element$/
   */
  public function iShouldSeeOnTheSelectElement($num, $locator){
    $handler = $this->getSession()->getSelectorsHandler();
    $optionElements = $this->getSession()->getPage()->findAll('named', array('option', $handler->selectorToXpath('css', $locator)));
    if (count($optionElements) != $num) {
      throw new Exception(sprintf('We should see "%s" options in "%s". But just "%s" was found ', $num, $locator, count($optionElements)));
    }
    return;
  }
  
  /**
	 * Fill field through class element - it is important when ID or Name of element (field) has the increment number
	 * Requires an "class" attribute to uniquely identify the element in the document.
	 *
	 * Example: Then I fill in ".class" field with "Test"
	 *
	 * @Then /^I fill in "(?P<locator>(?:[^"]|\\")*)" field with "(?P<text>(?:[^"]|\\")*)"$/
	*/
	 
	public function fillFieldThatHasIncrementID($locator, $value) {
		$page = $this->getSession()->getPage();
        $el = $page->find('css', $locator);

    if ($el === null){
      //sprintf("element not found %s", $el);
      throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    $class = $el->getAttribute('class');
		if(empty($class)) {
			throw new \InvalidArgumentException('Element requires an "class" attribute');
		}
		$el->setValue($value);
	}

}
