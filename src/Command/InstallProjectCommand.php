<?php
namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Class InstallProjectCommand
 *
 * @package App\Command
 * @desc By running this command you are installing the Take Home Task project
 * @desc
 */
class InstallProjectCommand extends Command
{
    protected static $defaultName = 'gimmemore:install';

    private SymfonyStyle $io;

    private string $projectDir;

    private int $timeout = 600;

    public function __construct(string $projectDir)
    {
        set_time_limit(0);

        parent::__construct();
        $this->projectDir = $projectDir;
    }

    protected function configure()
    {
        $this->setDescription('Install gimmemore project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $input->setInteractive(false);

        $this->io = $io;

        try {
            $this->runComposerInstall();
            $this->runDropDatabase();
            $this->runCreateDatabase();
            $this->runDoctrineSchemaUpdate();
            $this->runDoctrineFixturesLoad();
            $this->runYarnInstall();
            $this->runYarnBuild();

            return Command::SUCCESS;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return void
     */
    protected function runComposerInstall(): void
    {
        $process = new Process(['composer', 'install'], $this->projectDir);

        $this->runProcessBlockCommands($process, 'Trying to execute composer install ...');
    }


    /**
     * @return void
     */
    protected function runDropDatabase(): void
    {
        $process = new Process(['bin/console', 'doctrine:database:drop', '--if-exists', '--force'], $this->projectDir);

        $this->runProcessBlockCommands($process, 'Trying to drop database ...');
    }

    /**
     * @return void
     */
    protected function runCreateDatabase(): void
    {
        $process = new Process(['bin/console', 'doctrine:database:create'], $this->projectDir);

        $this->runProcessBlockCommands($process, 'Trying to create database ...');
    }

    /**
     * @return void
     */
    protected function runDoctrineSchemaUpdate(): void
    {
        $process = new Process(
            [
                'bin/console',
                'doctrine:schema:update',
                '--no-interaction',
                '--force',
                '-vvv',
            ], $this->projectDir
        );

        $this->runProcessBlockCommands(
            $process,
            'Trying to update database schema ...',
            'Successfully database schema created'
        );
    }

    /**
     * @return void
     */
    protected function runDoctrineFixturesLoad(): void
    {
        $process = new Process(
            [
                'bin/console',
                'doctrine:fixtures:load',
                '--no-interaction',
                '-vvv',
            ], $this->projectDir
        );

        $this->runProcessBlockCommands($process, 'Trying to load Doctrine fixtures ...');
    }

    protected function runYarnInstall(): void
    {
        $process = new Process(['yarn', 'install', '--check-files'], $this->projectDir);

        $this->runProcessBlockCommands($process, 'Trying to execute yarn install ...');
    }

    protected function runYarnBuild(): void
    {
        $process = new Process(['yarn', 'run', 'build'], $this->projectDir);

        $this->runProcessBlockCommands($process, 'Trying to execute yarn run build ...');
    }

    /**
     * @param Process $process
     * @param string $message
     * @param string $successOutput
     * @return void
     */
    protected function runProcessBlockCommands(Process $process, string $message, $successOutput = ''): void
    {
        $this->io->newLine(2);
        $this->displayNote($message);

        $process->setTimeout($this->timeout);

        $this->runProcess($process);

        if (empty($successOutput)) {
            $this->displayProcessSuccessOutput($process);
        } else {
            $this->displaySimpleSuccessOutput($successOutput);
        }

        $this->io->newLine(2);
    }

    /**
     * @param $message
     */
    protected function displayNote($message): void
    {
        $this->io->text('<fg=yellow;bg=black>' . $message . '</>');
    }

    /**
     * @param Process $process
     */
    protected function runProcess(Process $process): void
    {
        $process->mustRun();
    }

    /**
     * @param Process $process
     */
    protected function displayProcessSuccessOutput(Process $process): void
    {
        $processIterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);

        foreach ($processIterator as $iteratorOutput) {
            $this->io->success($iteratorOutput);
        }
    }

    /**
     * @param string $successOutput
     */
    protected function displaySimpleSuccessOutput(string $successOutput)
    {
        $this->io->success($successOutput);
    }
}