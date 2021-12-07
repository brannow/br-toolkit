<?php

namespace BR\Toolkit\Typo3\Cache\Command;

use BR\Toolkit\Typo3\Cache\CacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheCommand extends Command
{
    /**
     * @var CacheService
     */
    private $cacheService;

    /**
     * @param CacheService $cacheService
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('list', InputArgument::OPTIONAL, 'List Cache Context, Keys, TTL and Sizes (only work in Debug Context)', '');
        $this->addArgument('show', InputArgument::OPTIONAL, 'Show Cache Context Content (only work in Debug Context)', '');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $verbose = $input->getOption('verbose');


        if ($input->getArgument('list') === 'list') {
            foreach ($this->cacheService->debugGetCacheContextList() as $context) {
                $this->processCacheContext($context, $output);
            }
        } elseif ($input->hasArgument('show')) {
            $contextName = $input->getArgument('show');
            $this->printCacheContext($contextName, $output, $verbose);
        }


        return Command::SUCCESS;
    }

    /**
     * @param string $context
     * @param OutputInterface $output
     * @param bool $verbose
     * @return void
     */
    protected function printCacheContext(string $context, OutputInterface $output, bool $verbose = false): void
    {
        $this->processCacheContext($context, $output, $verbose);
    }

    /**
     * @param string $cacheContext
     * @param OutputInterface $output
     * @param bool $details
     * @return void
     */
    protected function processCacheContext(string $cacheContext, OutputInterface $output, bool $details = false): void
    {
        $output->writeln('');
        $output->writeln('--- ' . $cacheContext . ' --- ');
        $content = $this->cacheService->debugGetCacheContextContent($cacheContext);
        $output->writeln('Size: '."\t~" . $this->human_filesize(strlen(serialize($content)), 2));
        $output->writeln('Count: '."\t " . count($content));
        $output->writeln('');

        $output->write('key');
        $output->write("\t\t\t\t".'type');
        $output->writeln("\t".'ttl');
        $output->writeln("\t".'size');
        $output->writeln('----------------------------------------------------------------------------');

        foreach ($content as $key => $cacheItem) {
            $this->processCacheContentItem($cacheContext, $key, $cacheItem, $output, $details);
        }
        $output->writeln('');
    }

    /**
     * @param string $context
     * @param string $key
     * @param array $contentItem
     * @param OutputInterface $output
     * @param bool $details
     * @return void
     */
    protected function processCacheContentItem(string $context, string $key, array $contentItem, OutputInterface $output, bool $details): void
    {
        if ($contentItem['raw'] === false) {
            $rawContent = serialize($contentItem['content']);
            $d = $contentItem['content'];
            $type = gettype($contentItem['content']);
        } else {
            $rawContent = $contentItem['content'];
            $d = unserialize($contentItem['content']);
            $type = gettype($d);
        }

        if ($type === 'object') {
            $type .= '('.get_class($d).')';
        }


        $output->write($key);
        $output->write("\t");
        $output->write($type);
        $output->write("\t");
        $output->write($contentItem['ttl']);
        $output->write("\t");
        $output->writeln('~'.$this->human_filesize(strlen($rawContent)));

        if ($details) {
            $this->showItemDetails($contentItem, $output);
        }
    }

    /**
     * @param array $contentItem
     * @param OutputInterface $output
     * @return void
     */
    protected function showItemDetails(array $contentItem, OutputInterface $output): void
    {
        if ($contentItem['raw'] === false) {
            $d = $contentItem['content'];
        } else {
            $d = unserialize($contentItem['content']);
        }
        $output->writeln('');
        $output->writeln(print_r($d, true));
        $output->writeln('--------------');
    }

    /**
     * @param int $bytes
     * @param int $dec
     * @return string
     */
    private function human_filesize(int $bytes, int $dec = 0): string
    {
        $size   = array('B', 'kiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) .' '. @$size[$factor];
    }
}