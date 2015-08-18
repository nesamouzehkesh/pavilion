<?php

namespace Saman\CronBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Saman\CronBundle\Helpers\LoremIpsumGenerator;

/**
 * This command is to pre populating performances with input files
 *
 */
class DummyPopulateCommand extends ContainerAwareCommand 
{
    private $entities = array(
        'saman_theme',
        'saman_page',
        'saman_product',
        'saman_navigation',
    );
    
    /**
     * Configuring the command
     */
    protected function configure()
    {
        $this
            ->setName('run:dummyPopulate --env=XXX')
            ->setDescription('Populate current existed performances based on provided input file')
            ->addArgument(
                'entity',
                InputArgument::OPTIONAL,
                'Enter the entity you want to populate it (e.g Theme)'
                )
            ->addArgument(
                'number',
                InputArgument::OPTIONAL,
                'Enter number of fummy data you want to create'
                )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'Enter dummy data into all entities'
                )
            ->addOption(
                'tr',
                null,
                InputOption::VALUE_NONE,
                'Truncate data from entities'
                );
    }
    
    /**
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            // Get all command parameters
            $commandParameters = $this->getCommandParameters($input, $output);
            $entities = $commandParameters['entities'];
            $number = $commandParameters['number'];
            
            $this->truncateData($input, $output, $entities);
            
            /**
             * The Progress Helper was deprecated in Symfony 2.5 and will be 
             * removed in Symfony 3.0. We should use the Progress Bar 
             * instead which is more powerful.
             */
            $progressHelper = $this->getHelper('progress');
            foreach ($entities as $entity) {
                $output->writeln(sprintf('<info>Insert dummy data into:</info> %s', $entity));
                $progressHelper->start($output, $number);
                for ($i = 0; $i < $number; $i++) {
                    $progressHelper->advance();
                    $this->insertDummyData($output, $entity);
                }
                $progressHelper->finish();
            }
        } catch (\Exception $ex) {
            $output->writeln($ex);
        }
    }
    
    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param type $entities
     * @return type
     */
    private function truncateData(InputInterface $input, OutputInterface $output, $entities)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $options = $input->getOptions();
        
        if (!$options['tr']) {
            return;
        }
        
        $output->writeln('');
        $doctrine->getConnection()
            ->prepare('SET foreign_key_checks = 0;')
            ->execute();            
        
        foreach ($entities as $entity) {
            $output->writeln(sprintf('<error>TRUNCATE %s</error>', $entity));
            
            switch ($entity) {
                case 'saman_page':
                    $doctrine->getConnection()->prepare('TRUNCATE saman_page;')->execute();
                    $doctrine->getConnection()->prepare('TRUNCATE saman_page_label;')->execute();
                    break;
                case 'saman_product':
                    $doctrine->getConnection()->prepare('TRUNCATE saman_product;')->execute();
                    $doctrine->getConnection()->prepare('TRUNCATE saman_product_categories;')->execute();
                    $doctrine->getConnection()->prepare('TRUNCATE saman_product_label;')->execute();
                    break;
                case 'saman_theme':
                    $doctrine->getConnection()->prepare('TRUNCATE saman_theme;')->execute();
                    $doctrine->getConnection()->prepare('TRUNCATE saman_theme_label;')->execute();
                    $doctrine->getConnection()->prepare('TRUNCATE saman_widget;')->execute();
                    break;
                case 'saman_navigation':
                    $doctrine->getConnection()->prepare('TRUNCATE saman_navigation;')->execute();
                    $doctrine->getConnection()->prepare('TRUNCATE saman_link;')->execute();
                    
                    break;
            }
        }
        
        $output->writeln('');
        $doctrine->getConnection()
            ->prepare('SET foreign_key_checks = 1;')
            ->execute();            
    }
    
    /**
     * 
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param type $entity
     */
    private function insertDummyData(OutputInterface $output, $entity)
    {
        $generator = new LoremIpsumGenerator();
        /** @var Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');
        $date = new \DateTime();
        
        $createTimestamp = $date->getTimestamp() - rand(100, 10000);
        $modifiedTimestamp = $createTimestamp + rand(100, 10000);
            
        switch ($entity) {
            case 'saman_page':
                $title = $generator->getContent(rand(2, 4), 'plain');
                $content = $generator->getContent(rand(20 , 200), 'html');
                $url = rtrim(implode('/', explode(' ', $generator->getContent(rand(2, 4), 'plain'))), "/");

                $query = "INSERT INTO `%s` "
                    . "(`id`, `title`, `content`, `url`, `theme_id`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`, `visibility`) "
                    . "VALUES "
                    . "(NULL, '%s', '%s', '%s', NULL, '%d', '%d', '0', NULL, '0');";

                $stmt = $doctrine->getConnection()
                    ->prepare(sprintf($query, $entity, $title, $content, $url, $createTimestamp, $modifiedTimestamp));
                $stmt->execute();
            
                break;
            case 'saman_theme':
                $title = $generator->getContent(rand(2, 4), 'plain');
                $description = $generator->getContent(rand(20 , 40), 'plaintxt');

                $query = "INSERT INTO `%s` "
                    . "(`id`, `title`, `content`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`, `visibility`, `description`, `template`) "
                    . "VALUES "
                    . "(NULL, '%s', NULL, '%d', '%d', '0', NULL, '0', '%s', 'default');";

                $stmt = $doctrine->getConnection()
                    ->prepare(sprintf($query, $entity, $title, $createTimestamp, $modifiedTimestamp, $description));
                $stmt->execute();
            
                break;
            case 'saman_product':
                $title = $generator->getContent(rand(2, 4), 'plain');
                $content = $generator->getContent(rand(20 , 40), 'plaintxt');
                $price = rand(100 , 1000);
                $viewed = rand(1000 , 100000);
                    
                $query = "INSERT INTO `%s` "
                    . "(`id`, `title`, `content`, `price`, `viewed`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`, `visibility`, `image`) "
                    . "VALUES "
                    . "(NULL, '%s', '%s', '%d', '%d', '%d', '%d', '0', NULL, '0', NULL);";

                $stmt = $doctrine->getConnection()
                    ->prepare(sprintf($query, $entity, $title, $content, $price, $viewed, $createTimestamp, $modifiedTimestamp));
                $stmt->execute();
            
                break;
            case 'saman_navigation':
                $title = $generator->getContent(rand(2, 4), 'plain');
                $url = $generator->getContent(rand(2, 4), 'url');
                
                $query = "INSERT INTO `%s` "
                    . "(`id`, `title`, `description`, `content`, `settings`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`, `visibility`) "
                    . "VALUES "
                    . "(NULL, '%s', '', '', 'N;', '%d', '%d', '0', NULL, '0');";
                
                $db = $doctrine->getConnection();
                $stmt = $db->prepare(sprintf($query, $entity, $title, $createTimestamp, $modifiedTimestamp));
                $stmt->execute();
                $navigationId = $db->lastInsertId();
                
                for ($i = 0; $i < 5; $i++) {
                    $title = $generator->getContent(rand(2, 4), 'plain');
                    $query = "INSERT INTO `saman_link` "
                        . "(`id`, `sort`, `navigation_id`, `parent_id`, `title`, `hint`, `url`, `settings`, `created_time`, `modified_time`, `is_deleted`, `deleted_time`, `visibility`) "
                        . "VALUES "
                        . "(NULL, '%d', '%d', NULL, '%s', '%s', '%s', 'N;', '%d', '%d', '0', NULL, '0');";

                    $stmt = $doctrine->getConnection()
                        ->prepare(sprintf($query, $i, $navigationId, $title, $title, $url, $createTimestamp, $modifiedTimestamp));
                    $stmt->execute();
                }
            
                break;            
        }
    }
    
    /**
     * Get all comand parameters from concole or in command
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return type
     */
    private function getCommandParameters(InputInterface $input, OutputInterface $output)
    {
        /**
         * The Dialog Helper was deprecated in Symfony 2.5 and will be 
         * removed in Symfony 3.0. We should use the Question Helper 
         * instead, which is simpler to use.
         */
        $dialogHelper = $this->getHelper('dialog');
        $number = $input->getArgument('number');
        $options = $input->getOptions();
        
        $entities = array();
        if ($options['all']) {
            $entities = $this->entities;
        } else {
            $entity = $input->getArgument('entity');
            if (null === $entity) {
                foreach ($this->entities as $title) {
                    $output->writeln(sprintf('%s', $title));
                }
                $entity = $dialogHelper->ask(
                    $output, 
                    '<info>Enter the entity you want to populate it from the above list (Defult is all):</info>'
                    );
                if ('all' === $entity || null === $entity) {
                    $entities = $this->entities;
                } else {
                    $entities[] = $entity;
                }
            } else {
                $entities[] = $entity;
            }
        }
        
        if (null === $number) {
            $number = $dialogHelper->ask(
                $output, 
                '<info>Enter number of fummy data you want to create (Deafult value is 10):</info>'
                );
            if (null === $number) {
                $number = 10;
            }
        }
        
        return array(
            'entities' => $entities,
            'number' => $number,
            );
    }
}