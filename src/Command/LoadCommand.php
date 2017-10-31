<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 10:01
 */

namespace App\Command;

use App\Entity\Choice;
use App\Entity\Gabier;
use App\Entity\LEG;
use Buzz\Browser;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCommand extends Command implements ContainerAwareInterface, LoggerAwareInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('app:load')
            ->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        $csv = $this->getData($force);
        $this->clean();
        $this->parseGabiers($csv);
        $this->parseChoices($csv);
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
            $questionnaire = $this->container->getParameter(
                'app.questionnaire'
            );
            $resultat = $this->container->getParameter('app.resultat');
            $url = sprintf(
                'http://www.askabox.fr/resultats.php?exportcsv=Export+CSV&s=%s&r=%s',
                $questionnaire,
                $resultat
            );

            $browser = new Browser();
            $headers = [
                'Host' => 'www.askabox.fr',
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:56.0) Gecko/20100101 Firefox/56.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ];
            $response = $browser->get($url, $headers);
            $csv = $response->getContent();
            // Fix latin1 encoding
            $csv = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $csv);
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

    private function clean()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $choices = $em->getRepository('App:Choice')->findAll();
        foreach ($choices as $choice) {
            $em->remove($choice);
        }
        $garbiers = $em->getRepository('App:Gabier')->findAll();
        foreach ($garbiers as $garbier) {
            $em->remove($garbier);
        }
        $em->flush();
    }

    private function parseGabiers($csv)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $gabiers = [];

        // Just keep NOM section
        $lines = $this->filter($csv, '"1. NOM"');

        foreach ($lines as $line) {
            if (preg_match('/^"(.*)";"(.*)"$/', $line, $matches) <= 0) {
                continue;
            }
            $pseudo = $matches[1];

            if (isset($gabiers[$pseudo])) {
                $gabier = $gabiers[$pseudo];
            } else {
                $gabier = new Gabier();
                $gabier->setPseudo($pseudo);
                $gabiers[$pseudo] = $gabier;
            }

            $gabier->setLastName($matches[2]);
        }

        // Just keep PRENOM section
        $lines = $this->filter($csv, '"2. PRÉNOM"');

        foreach ($lines as $line) {
            if (preg_match('/^"(.*)";"(.*)"$/', $line, $matches) <= 0) {
                continue;
            }
            $pseudo = $matches[1];

            if (isset($gabiers[$pseudo])) {
                $gabier = $gabiers[$pseudo];
            } else {
                $gabier = new Gabier();
                $gabier->setPseudo($pseudo);
                $gabiers[$pseudo] = $gabier;
            }

            $gabier->setFirstName($matches[2]);
        }

        foreach ($gabiers as $gabier) {
            $em->merge($gabier);
        }

        $em->flush();
    }

    private function parseChoices($csv)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        // Just keep responses
        $lines = $this->filter(
            $csv,
            '"10. Classez par ordre de préférence le(s) LEG(S) que vous souhaitez réaliser:"'
        );

        $temp = [];
        $legs = $em->getRepository("App:LEG")->findAll();
        foreach ($legs as $leg) {
            $temp[$leg->getId()] = $leg;
        }
        $legs = $temp;

        $gabiers = $em->getRepository("App:Gabier")->findAll();
        foreach ($gabiers as $gabier) {
            $temp[$gabier->getPseudo()] = $gabier;
        }
        $gabiers = $temp;

        foreach ($lines as $line) {
            if (preg_match('/^"(.*)";"(.*)"$/', $line, $matches) <= 0) {
                continue;
            }
            $pseudo = $matches[1];
            $choices = $matches[2];

            if (isset($gabiers[$pseudo])) {
                $gabier = $gabiers[$pseudo];
            } else {
                $this->logger->warning(sprintf('Gabier %s not found', $pseudo));
                continue;
            }

            foreach (explode("|", $choices) as $strChoice) {
                preg_match(
                    "/LEG (\d+) - (.+) : priorité (\d)/",
                    $strChoice,
                    $matches
                );

                $legId = $matches[1];
                $legName = $matches[2];
                $priority = $matches[3];

                if (isset($legs[$legId])) {
                    $leg = $legs[$legId];
                } else {
                    $this->logger->warning(
                        sprintf('New LEG found %s', $legId)
                    );
                    $leg = new LEG();
                    $leg->setId($legId);
                    $leg->setName($legName);
                    $legs[$legId] = $leg;
                    $em->persist($leg);
                }

                $choice = new Choice();
                $choice->setLEG($leg);
                $choice->setPriority($priority);
                $choice->setGabier($gabier);
                $leg->addChoice($choice);

                $gabier->addChoice($choice);
            }
        }

        foreach ($gabiers as $gabier) {
            $em->merge($gabier);
        }

        $em->flush();

        $this->fixChoiceZero();
    }

    private function filter($csv, $begin, $skipLines = 9)
    {
        $section = false;
        $lines = [];
        foreach (explode("\n", $csv) as $line) {
            if (!$section && $line != $begin) {
                continue;
            } else {
                $section = true;
            }
            if ($skipLines > 0) {
                $skipLines--;
                continue;
            }
            if ($line == "") {
                break;
            }
            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function fixChoiceZero()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $repo = $em->getRepository('App:Gabier');

        $qb = $repo->createQueryBuilder('g');
        $qb->join('g.choices', 'c');
        $qb->andWhere(
            $qb->expr()->eq('c.priority', ':priority')
        );
        $qb->orderBy('c.priority', 'ASC');
        $qb->setParameter('priority', 0);
        $gabiers = $qb->getQuery()->execute();

        foreach ($gabiers as $gabier) {
            foreach ($gabier->getChoices() as $choice) {
                $choice->setPriority($choice->getPriority() + 1);
            }
        }
        $em->flush();
    }

    protected function slugify($text)
    {
        $text = trim($text);
        // this code is for BC
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        /*if (function_exists('iconv')) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        }*/
        $text = transliterator_transliterate("Latin-ASCII", $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text;
    }
}