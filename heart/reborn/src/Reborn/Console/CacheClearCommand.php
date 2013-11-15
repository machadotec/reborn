<?php

namespace Reborn\Console;

use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;
use Symfony\Component\Console\Command\Command as SfCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cache Clear Command class for Reborn
 *
 * @package Reborn\Console
 * @author Myanmar Links Professional Web Development Team
 **/
class CacheClearCommand extends SfCommand
{

    /**
     * Cache Storage Folder Path
     *
     * @var string
     **/
    protected $path = STORAGES;

    /**
     * Cache folder lists
     *
     * @var array
     **/
    protected $lists = array('cache', 'logs', 'maps', 'template', 'tmp');

    /**
     * Skip files list for cache clear
     *
     * @var array
     **/
    protected $skips = array('.gitignore', '.gitkeep');

    /**
     * Configures the current command.
     */
	protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clear Application Caches');

        $this->addOption('folder', 'f', InputOption::VALUE_OPTIONAL, 'Cache Folder name');
    }

    /**
     * Executes the current command.
     *
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$folder = $input->getOption('folder');

        if (is_null($folder)) {
            $this->clearAll();
        } else {
            $this->clearChoose($folder);
        }

        $name = is_null($folder) ? 'All' : ucfirst($folder);

		$output->writeln("<info>Cache Cleared Successfully for $name!</info>");
    }

    /**
     * Clear All Cache
     *
     * @return void
     **/
    protected function clearAll()
    {
        foreach ($this->lists as $list) {
            $this->clearChoose($list);
        }
    }

    /**
     * Clear Cache from choose folder
     *
     * @param string $folder Folder path
     * @return void
     **/
    protected function clearChoose($folder)
    {
        if (in_array($folder, $this->lists)) {
            if (Dir::is($this->path.$folder.DS)) {
                Dir::delete($this->path.$folder.DS, false, $this->skips);
            }
        }
    }

} // END class Console