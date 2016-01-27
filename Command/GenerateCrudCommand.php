<?php

namespace Symfonian\Indonesia\AdminBundle\Command;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Generator\FormGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GenerateCrudCommand extends GenerateDoctrineCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)'),
                new InputOption('overwrite', '', InputOption::VALUE_NONE, 'Overwrite any existing controller or form class when generating the CRUD contents'),
            ))
            ->setName('siab:generate:crud')
            ->setAliases(array('siab:generate', 'siab:crud:generate'))
            ->setDescription('Generate CRUD from Entity using Symfonian Indonesia Admin Bundle style')
            ->setHelp(<<<EOT
The <info>siab:generate:crud</info> command generates a CRUD based on a Doctrine entity using Symfonian Indonesia Admin Bundle style.

<info>php bin/console siab:generate:crud --entity=AcmeBlogBundle:Post</info>

Every generated file is based on a template. There are default templates but they can be overriden by overriding config parameters.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getOption('entity'));
        $forceOverwrite = $input->getOption('overwrite');
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        try {
            $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
            $metadata = $this->getEntityMetadata($entityClass);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Entity "%s" does not exist in the "%s" bundle. Create it before and then execute this command again.', $entity, $bundle));
        }
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        /** @var FormGenerator $generator */
        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $entity, $metadata[0], $forceOverwrite);
    }

    /**
     * @return FormGenerator
     */
    protected function createGenerator()
    {
        /** @var \Symfony\Component\Filesystem\Filesystem $fileSystem */
        $fileSystem = $this->getContainer()->get('filesystem');
        return new FormGenerator($fileSystem);
    }
}