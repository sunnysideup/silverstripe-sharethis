<?php

namespace SunnysideUp\ShareThis;

use SilverStripe\Dev\SapphireTest;

/**
 * SharethisTest
 */
class SharethisTest extends SapphireTest
{
	/**
	 * @var boolean
	 */
    protected $usesDatabase = false;

    /**
     * @var array
     */
    protected $requiredExtensions = [];

    /**
     * Test the dev build
     */
    public function TestDevBuild()
    {
        $exitStatus = shell_exec('php framework/cli-script.php dev/build flush=all  > dev/null; echo $?');
        $exitStatus = intval(trim($exitStatus));
        $this->assertEquals(0, $exitStatus);
    }
}
