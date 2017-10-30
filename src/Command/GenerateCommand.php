<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 10:01
 */
namespace App\Command;

use Buzz\Browser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateCommand extends Command implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('app:generate')
            ->addOption('force', 'f', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csv = $this->getData($input->getOption('force'));

        $section = '"10. Classez par ordre de préférence le(s) LEG(S) que vous souhaitez réaliser:"';

        $pos = strpos($csv, $section);

        var_dump($pos);

    }

    /**
     * @param bool $force
     *
     * @return string
     */
    protected function getData($force = false)
    {
        $file = 'data.csv';
        $force = $force || !file_exists($file);
        if ($force) {
            $url = $this->container->getParameter('app.url');

            $browser  = new Browser();
            $headers = [
                'Host' => 'www.askabox.fr',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:56.0) Gecko/20100101 Firefox/56.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ];
            $response = $browser->get($url, $headers);
            $csv      = $response->getContent();
            file_put_contents($file, $csv);

            return $csv;
        } else {
            return file_get_contents($file);
        }
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}