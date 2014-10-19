<?php

namespace spec\Envariable\FrameworkConfigPathLocatorCommands;

use Envariable\Util\Filesystem;
use PhpSpec\ObjectBehavior;

/**
 * CodeIgniter Config Path Locator Command Test.
 *
 * @author Mark Kasaboski <mark.kasaboski@gmail.com>
 */
class CodeIgniterConfigPathLocatorCommandSpec extends ObjectBehavior
{
    /**
     * Test that the SUT is initializable.
     */
    function it_is_initializable()
    {
        $this->shouldHaveType('Envariable\FrameworkConfigPathLocatorCommands\CodeIgniterConfigPathLocatorCommand');
    }

    /**
     * Test that it locates the path to the CodeIgniter config directory.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_locates_the_codeigniter_config_directory_path(Filesystem $filesystem)
    {
        $frontControllerPath    = 'path/to/index.php';
        $frontControllerContent = <<<FC
<?php

\$system_path = 'system';
\$application_path = 'application';

require_once BASEPATH.'core/CodeIgniter.php';
FC;

        $filesystem
            ->getApplicationRootPath()
            ->willReturn('path/to');

        $filesystem
            ->fileGetContents($frontControllerPath)
            ->willReturn($frontControllerContent);

        $this->setFilesystem($filesystem);
        $this->locate()->shouldReturn('path/to/application/config');
    }

    /**
     * Test that it locates the path to the CodeIgniter config directory with a
     * different application directory name.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_locates_the_codeigniter_config_directory_path_with_alternate_apppath_name(Filesystem $filesystem)
    {
        $frontControllerPath    = 'path/to/index.php';
        $frontControllerContent = <<<FC
<?php

\$system_path = 'system';
\$application_path = 'alternate';

require_once BASEPATH.'core/CodeIgniter.php';
FC;

        $filesystem
            ->getApplicationRootPath()
            ->willReturn('path/to');

        $filesystem
            ->fileGetContents($frontControllerPath)
            ->willReturn($frontControllerContent);

        $this->setFilesystem($filesystem);
        $this->locate()->shouldReturn('path/to/alternate/config');
    }

    /**
     * Test that it returns null as it determines that it is not dealing
     * with a CodeIgniter front controller
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_returns_null_not_a_codeigniter_front_controller(Filesystem $filesystem)
    {
        $frontControllerPath    = 'path/to/index.php';
        $frontControllerContent = <<<FC
<?php

\$system_path = 'system';
\$application_path = 'application';
FC;

        $filesystem
            ->getApplicationRootPath()
            ->willReturn('path/to');

        $filesystem
            ->fileGetContents($frontControllerPath)
            ->willReturn($frontControllerContent);

        $this->setFilesystem($filesystem);
        $this->locate()->shouldReturn(null);
    }

    /**
     * Test that an exception is thrown in the event that the application path is not found.
     *
     * @param \Envariable\Util\Filesystem $filesystem
     */
    function it_throws_an_exception_as_application_path_not_found(Filesystem $filesystem)
    {
        $frontControllerPath    = 'path/to/index.php';
        $frontControllerContent = <<<FC
<?php

\$system_path = 'system';
\$renamed_application_path = 'alternate';

require_once BASEPATH.'core/CodeIgniter.php';
FC;

        $filesystem
            ->getApplicationRootPath()
            ->willReturn('path/to');

        $filesystem
            ->fileGetContents($frontControllerPath)
            ->willReturn($frontControllerContent);

        $this->setFilesystem($filesystem);
        $this->shouldThrow(new \RuntimeException('Application path could not be found.'))->duringLocate($frontControllerPath);
    }
}
