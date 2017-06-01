<?php
namespace Robo\Task\Composer;

use Robo\Contract\CommandInterface;
use Robo\Exception\TaskException;
use Robo\Task\BaseTask;

abstract class Base extends BaseTask implements CommandInterface
{
    use \Robo\Common\ExecOneCommand;

    /**
     * @var string
     */
    protected $command = '';

    /**
     * @var boolena
     */
    protected $built = false;

    /**
     * @var string
     */
    protected $prefer;

    /**
     * @var string
     */
    protected $dev;

    /**
     * @var string
     */
    protected $ansi;

    /**
     * @var string
     */
    protected $nointeraction;

    /**
     * Action to use
     *
     * @var string
     */
    protected $action = '';

    /**
     * adds `prefer-dist` option to composer
     *
     * @return $this
     */
    public function preferDist()
    {
        $this->prefer = '--prefer-dist';
        return $this;
    }

    /**
     * adds `prefer-source` option to composer
     *
     * @return $this
     */
    public function preferSource()
    {
        $this->prefer = '--prefer-source';
        return $this;
    }

    /**
     * adds `no-dev` option to composer
     *
     * @return $this
     */
    public function noDev()
    {
        $this->dev = '--no-dev';
        return $this;
    }

    /**
     * adds `dev` option to composer
     *
     * @return $this
     */
    public function dev()
    {
        $this->dev = '--dev';
        return $this;
    }

    /**
     * adds `no-ansi` option to composer
     *
     * @return $this
     */
    public function noAnsi()
    {
        $this->ansi = '--no-ansi';
        return $this;
    }

    /**
     * adds `ansi` option to composer
     *
     * @return $this
     */
    public function ansi()
    {
        $this->ansi = '--ansi';
        return $this;
    }

    /**
     * adds `no-interaction` option to composer
     *
     * @return $this
     */
    public function noInteraction()
    {
        $this->nointeraction = '--no-interaction';
    }

    /**
     * adds `optimize-autoloader` option to composer
     *
     * @return $this
     */
    public function optimizeAutoloader()
    {
        $this->option('--optimize-autoloader');
        return $this;
    }

    /**
     * adds `ignore-platform-reqs` option to composer
     *
     * @return $this
     */
    public function ignorePlatformRequirements()
    {
        $this->option('--ignore-platform-reqs');
        return $this;
    }

    /**
     * disable plugins
     *
     * @return $this
     */
    public function disablePlugins()
    {
        $this->option('--no-plugins');
        return $this;
    }

    /**
     * adds `--working-dir $dir` option to composer
     *
     * @return $this
     */
    public function workingDir($dir)
    {
        $this->option("--working-dir", $dir);
        return $this;
    }

    /**
     * @param null|string $pathToComposer
     *
     * @throws \Robo\Exception\TaskException
     */
    public function __construct($pathToComposer = null)
    {
        $this->command = $pathToComposer;
        if (!$this->command) {
            $this->command = $this->findExecutablePhar('composer');
        }
        if (!$this->command) {
            throw new TaskException(__CLASS__, "Neither local composer.phar nor global composer installation could be found.");
        }
    }

    /**
     * Copy class fields into command options as directed.
     */
    public function buildCommand()
    {
        if (!isset($this->ansi) && $this->getConfig()->isDecorated()) {
            $this->ansi();
        }
        if (!isset($this->nointeraction) && !$this->getConfig()->isInteractive()) {
            $this->noInteraction();
        }
        $this->option($this->prefer)
            ->option($this->dev)
            ->option($this->nointeraction)
            ->option($this->ansi);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        if (!$this->built) {
            $this->buildCommand();
            $this->built = true;
        }
        return "{$this->command} {$this->action}{$this->arguments}";
    }
}
