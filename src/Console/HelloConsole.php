<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace Console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DemoConsole
 * @package Console
 */
class HelloConsole extends Command
{
    public function configure()
    {
        $this->setName('hello');
        $this->addArgument('name', InputArgument::OPTIONAL, '', 'jan');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('hello '.$input->getArgument('name'));
    }
}