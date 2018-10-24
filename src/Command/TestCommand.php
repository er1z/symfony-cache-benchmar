<?php


namespace App\Command;


use App\Tester;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    /**
     * @var Tester
     */
    private $tester;

    /**
     * TestCommand constructor.
     */
    public function __construct(Tester $tester)
    {
        parent::__construct();
        $this->tester = $tester;
    }

    protected function configure()
    {
        $this->setName('app:test');
        $this->addArgument('iterations', InputArgument::OPTIONAL, '', 1000);
        $this->addOption('stages', 's', InputOption::VALUE_OPTIONAL, '', 3);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('Benchmark in progress, stand by...');

        for($a=1;$a<=$input->getOption('stages');$a++){

            $output->writeln(sprintf('Stage: %d', $a));

            $results = $this->tester->run(
                $input->getArgument('iterations')
            );

            $table = new Table($output);
            $table
                ->setHeaders(array_merge(['Adapter'], array_keys(array_values($results)[0])))
            ;

            foreach($results as $k=>$r){
                $table->addRow(array_merge([$k], array_values($r)));
            }

            $table->render();
        }
    }


}