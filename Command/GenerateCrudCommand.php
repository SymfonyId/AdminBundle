<?php

/*
 * This file is part of the AdminBundle package.
 *
 * (c) Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonian\Indonesia\AdminBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfonian\Indonesia\AdminBundle\Generator\ControllerGenerator;
use Symfonian\Indonesia\AdminBundle\Generator\FormGenerator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Muhammad Surya Ihsanuddin <surya.kejawen@gmail.com>
 */
class GenerateCrudCommand extends GenerateDoctrineCommand
{
    protected function configure()
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite any existing controller or form class when generating the CRUD contents')
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

        $entity = Validators::validateEntityName($input->getArgument('entity'));
        $forceOverwrite = $input->getOption('overwrite');
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        try {
            $metadata = $this->getEntityMetadata($entityClass);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Entity "%s" does not exist in the "%s" bundle. Create it before and then execute this command again.', $entity, $bundle));
        }
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        /** @var FormGenerator $formGenerator */
        $formGenerator = $this->getGenerator($bundle);
        $formGenerator->generate($bundle, $entity, $metadata[0], $forceOverwrite);

        $output->writeln(sprintf('<info>Form type for entity %s has been generated</info>', $entityClass));

        $controllerGenerator = $this->getControllerGenerator($bundle);
        $controllerGenerator->generate($bundle, $entityClass, $metadata[0], $forceOverwrite);

        $output->writeln(sprintf('<info>Controller for entity %s has been generated</info>', $entityClass));

        /** @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $cacheClearCommand = $this->getApplication()->find('cache:clear');
        $cacheClearCommand->run(new ArrayInput(array('--env' => $kernel->getEnvironment())), $output);

        $output->writeln(sprintf('<info>CRUD Generation is successfully!</info>', $entityClass));
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

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SymfonianIndonesiaAdminBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        /** @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');
        if (is_dir($dir = $kernel->getRootDir().'/Resources/SymfonianIndonesiaAdminBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = __DIR__.'/../Resources/skeleton';
        $skeletonDirs[] = __DIR__.'/../Resources';

        return $skeletonDirs;
    }

    private function getControllerGenerator($bundle = null)
    {
        /** @var \Symfony\Component\Filesystem\Filesystem $fileSystem */
        $fileSystem = $this->getContainer()->get('filesystem');
        $generator = new ControllerGenerator($fileSystem);
        $generator->setSkeletonDirs($this->getSkeletonDirs($bundle));

        return $generator;
    }
}
