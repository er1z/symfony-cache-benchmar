<?php


namespace App;


use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class Tester
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters;
    /**
     * @var \Faker\Generator
     */
    private $generator;

    public function __construct($adapters, \Faker\Generator $generator)
    {
        $this->adapters = $adapters;
        $this->generator = $generator;
    }

    public function run($iterations = 1000)
    {
        $results = [];

        foreach($this->adapters as $a){
            $results[get_class($a)] = $this->testAdapter($a, $iterations);
        }

        return $results;
    }

    public function testAdapter(AdapterInterface $adapter, $iterations = 1000)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('write - miss');

        for($a=0;$a<$iterations;$a++){
            $item = $adapter->getItem((string)$a);
            $item->set(
                $this->getContentForInsertion()
            );

            $adapter->save($item);
        }

        $stopwatch->stop('write - miss');

        $stopwatch->start('write - hit');

        for($a=0;$a<$iterations;$a++){
            $item = $adapter->getItem((string)$a);
            $item->set(
                $this->getContentForInsertion()
            );

            $adapter->save($item);
        }

        $stopwatch->stop('write - hit');

        $stopwatch->start('read - hit');

        for($a=0;$a<$iterations;$a++){
            $adapter->getItem((string)$a);
        }
        $stopwatch->stop('read - hit');

        $stopwatch->start('read#2 - hit');

        for($a=0;$a<$iterations;$a++){
            $adapter->getItem((string)$a);
        }
        $stopwatch->stop('read#2 - hit');

        $adapter->clear();

        $stopwatch->start('read - miss');
        for($a=0;$a<$iterations;$a++){
            $adapter->getItem((string)$a);
        }
        $stopwatch->stop('read - miss');

        $result = $this->hydrateResults($stopwatch);

        return $result;
    }

    protected function hydrateResults(Stopwatch $stopwatch)
    {
        $events = $stopwatch->getSectionEvents('__root__');
        $result = [];

        foreach($events as $k=>$e){
            $result[$k] = $e->getDuration();
        }

        return $result;
    }

    protected function getContentForInsertion()
    {
        return implode(' ', [
            $this->generator->name,
            $this->generator->lastName,
            $this->generator->company,
            $this->generator->companyEmail
        ]);
    }
}