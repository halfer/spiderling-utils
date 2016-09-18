<?php

namespace halfer\SpiderlingUtils;

use \Openbuildings\Spiderling\Driver_Phantomjs_Connection;

/**
 * Let's have some declarations for magic methods
 *
 * @todo Can we move these upstream to PHPUnitSpiderling\Testcase_Spiderling?
 * 
 * @method \Openbuildings\Spiderling\Page visit($uri, array $query = array()) Initiate a visit with the currently selected driver
 * @method string content() Return the content of the last request from the currently selected driver
 * @method string current_path() Return the current browser url without the domain
 * @method string current_url() Return the current url
 * @method \Openbuildings\Spiderling\Node assertHasCss($selector, array $filters = array(), $message = NULL)
 * @method \Openbuildings\Spiderling\Node find($selector) Returns a single matching Node
 * @method \Openbuildings\Spiderling\Node not_present($selector) Checks that a CSS expression is not found
 * @method array all($selector) Returns all matching elements as Nodes
 * @method void screenshot($filename) Takes a screenshot at this point in time
 */
abstract class TestCase extends \Openbuildings\PHPUnitSpiderling\Testcase_Spiderling
{
	use \Awooga\Testing\BaseTestCase;

	const DOMAIN = 'http://127.0.0.1:8090';

	// Change this to turn logging back on
	const LOG_ACTIONS = false;

	/**
	 * Common library loading for all test classes
	 */
	public function setUp()
	{
		$this->buildDatabase($this->getDriver(false));
		$this->indexDocuments();
	}

	/**
	 * Let's set add some logging here, to see why PhantomJS is flaky on Travis
	 */
	public function driver_phantomjs()
	{
		$this->checkPhantomIsAvailable();

		// We can supply a log location here (or omit to use /dev/null)
		$logFile = '/tmp/phantom-awooga.log';
		$connection = new Driver_Phantomjs_Connection();
		$connection->start(null, self::LOG_ACTIONS ? $logFile : '/dev/null');

		$driver = new \Openbuildings\Spiderling\Driver_Phantomjs();
		$driver->connection($connection);

		$this->waitUntilPhantomStarts($connection);

		return $driver;
	}

	/**
	 * Throws a fatal exception if the PhantomJS executable is not found
	 * 
	 * @throws \Exception
	 */
	protected function checkPhantomIsAvailable()
	{
		$output = $return = null;
		exec('which phantomjs', $output, $return);
		if ($return)
		{
			throw new \Exception("Can't find 'phantomjs' - does the PATH include it?");
		}		
	}

	/**
	 * Try waiting to see if Travis can be made more robust
	 * 
	 * @param Driver_Phantomjs_Connection $connection
	 */
	protected function waitUntilPhantomStarts(Driver_Phantomjs_Connection $connection)
	{
		$i = 0;
		while (!$connection->is_running() || !$connection->is_running())
		{
			usleep(200000);
			if ($i++ > 10)
			{
				break;
			}
		}		
	}

	/**
	 * Creates the test database
	 * 
	 * @param \PDO $pdo
	 */
	protected function buildDatabase(\PDO $pdo)
	{
		$this->runSqlFile($pdo, $this->getProjectRoot() . '/test/build/init.sql');
		$this->runSqlFile($pdo, $this->getProjectRoot() . '/build/database/create.sql');
		$this->runSqlFile($pdo, $this->getProjectRoot() . '/test/browser/fixtures/data.sql');
	}

	/**
	 * Adds the fixtures reports to a test search index
	 */
	protected function indexDocuments()
	{
		// For now, if the index exists, let us not recreate it
		$indexPath = $this->getProjectRoot() . '/filesystem/tmp/search-index';
		if (file_exists($indexPath))
		{
			return;
		}

		$pdo = $this->getDriver();
		$statement = $pdo->prepare(
			'SELECT
				id, title, description_html
			FROM report WHERE is_enabled = 1'
		);
		$statement->execute();

		$searcher = new \Awooga\Core\Searcher();
		$searcher->connect($indexPath);

		while ($report = $statement->fetch(\PDO::FETCH_ASSOC))
		{
			$searcher->index(
				$report,
				$this->getUrls($pdo, $report['id']),
				$this->getIssues($pdo, $report['id'])
			);
		}
	}

	/**
	 * Returns the URLs related to the specified report in an array
	 * 
	 * @param \PDO $pdo
	 * @param integer $reportId
	 * @return array
	 */
	protected function getUrls(\PDO $pdo, $reportId)
	{
		$statement = $pdo->prepare(
			'SELECT
				url
			FROM
				resource_url
			WHERE
				report_id = :report_id'
		);

		$statement->execute(array(':report_id' => $reportId, ));
		$urls = array();
		while ($url = $statement->fetchColumn())
		{
			$urls[] = $url;
		}

		return $urls;
	}

	/**
	 * Returns the issues related to the specified report in an array
	 * 
	 * @param \PDO $pdo
	 * @param integer $reportId
	 * @return array
	 */
	protected function getIssues(\PDO $pdo, $reportId)
	{
		$statement = $pdo->prepare(
			'SELECT
				issue.code issue_cat_code,
				ri.description_html
			FROM report_issue ri
			INNER JOIN issue ON (ri.issue_id = issue.id)
			WHERE
				ri.report_id = :report_id'
		);
		$statement->execute(array(':report_id' => $reportId, ));

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Logs in the test user
	 */
	protected function loginTestUser()
	{
		// Logon and then check that it worked
		$this->visit(self::DOMAIN . '/auth?provider=test');
		$this->assertEquals('Logout testuser', $this->find('#auth-status')->text());
	}

	/**
	 * Waits until a redirect or a timeout occurs
	 * 
	 * @param string $originalUrl
	 * @return boolean Success
	 */
	protected function waitUntilRedirected($originalUrl)
	{
		$retry = 0;
		do {
			$hasRedirected = $originalUrl != $this->current_url();
			if (!$hasRedirected)
			{
				usleep(500000);
			}
		} while (!$hasRedirected && $retry++ < 20);

		return $hasRedirected;
	}

	/**
	 * Wait until the count of a selector agrees with the expected count, or a timeout occurs
	 * 
	 * @param string $selector
	 * @param integer $expectedCount
	 * @return boolean Success
	 */
	protected function waitForSelectorCount($selector, $expectedCount)
	{
		$retry = 0;
		do {
			$count = count($this->all($selector));
			$isReached = $count == $expectedCount;
			if (!$isReached)
			{
				usleep(500000);
			}
		} while (!$isReached && $retry++ < 20);

		return $isReached;
	}

	/**
	 * Takes a screenshot and appends it to a base64 log file
	 * 
	 * This is really handy for Travis, where exporting build artefacts like screenshots is
	 * not all that easy to do. We just cat the log file to the screen after the build, paste
	 * it into a file, and decode it locally.
	 * 
	 * @param string $title
	 */
	protected function encodedScreenshot($title)
	{
		$file = '/tmp/awooga-screenshot.png';
		$this->screenshot($file);
		$this->base64out($file, $title);
		unlink($file);
	}

	/**
	 * Base 64 encoding helper
	 * 
	 * @param string $filename
	 * @param string $title
	 * @throws \Exception
	 */
	protected function base64out($filename, $title)
	{
		if (!file_exists($filename))
		{
			throw new \Exception("File '$filename' not found");
		}

		$data = 
			"-----\n" .
			$title . "\n" .
			"-----\n" .
			chunk_split(base64_encode(file_get_contents($filename))) .
			"-----\n\n";
		file_put_contents('/tmp/awooga-screenshot-data.log', $data, FILE_APPEND);
	}
}
