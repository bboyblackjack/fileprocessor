<?php

class FileProcessorCommand extends CConsoleCommand {
    private function getChunk($resource) {
        $chunk = '';

        while (!feof($resource)) {
            $str = fgets($resource);
            if (strpos($str, '{')) {
                $chunk .= $str;
            } elseif (strpos($str, '}')) {
                $chunk .= $str;
                $chunk = str_replace(["\r", "\n"], '', $chunk);
                $chunk = ltrim($chunk, '[');
                $chunk = rtrim($chunk, '],');
                return $chunk;
            } elseif (strpos($chunk, '{')) {
                $chunk .= $str;
            }
        }
    }

    public function run($args) {
        ini_set('memory_limit', '2M');

        $conf = new RdKafka\Conf();
        $producer = new RdKafka\Producer($conf);
        $producer->addBrokers("kafka:9092");

        $topic = $producer->newTopic("fileprocessor");
        $producer->poll(1000);

        $firstTestFile = fopen(Yii::app()->getBasePath() . '/commands/test1.json', 'r');
        $secondTestFile = fopen(Yii::app()->getBasePath() . '/commands/test2.json', 'r');

        $resources = [$firstTestFile, $secondTestFile];

        $i = 0;
        $elementsCount = 0;

        $startExecutionTime = microtime(true);

        while (!empty($resources)) {
            $chunk = $this->getChunk($resources[$i]);
            if ($chunk) {
                $elementsCount++;
                $topic->produce(RD_KAFKA_PARTITION_UA, 0, $chunk);
            } else {
                unset($resources[$i]);
            }

            if (count($resources) == 1) {
                $i = array_keys($resources)[0];
            } else {
                $i = 1 - $i;
            }
        }

        $producer->flush(10000);

        $executionTime = microtime(true) - $startExecutionTime;

        echo "MEMORY USAGE: " . memory_get_usage() / 1024 / 1024 . " MB\n";
        echo "PEAK MEMORY USAGE: " . memory_get_peak_usage() / 1024 / 1024 . " MB\n";
        echo "ELEMENTS COUNT: " . $elementsCount . "\n";
        echo "EXECUTION TIME: " . $executionTime . "\n";
        echo "ELEMENTS PER SECOND PROCESSING: " . round($elementsCount / $executionTime) . "\n";
    }
}