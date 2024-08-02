<?php declare(strict_types=1);

namespace App\Service\Monolog;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Predis\Client as Predis;

/**
 * Logs to a Redis key using rpush
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisHandler(new Predis\Client("tcp://localhost:6379"), "logs");
 *   $log->pushHandler($redis);
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */
class RedisHandler extends AbstractProcessingHandler
{
    /** @var Predis<Predis> */
    private Predis $redisClient;
    private string $redisKey;
    protected int $capSize;

    /**
     * @param Predis<Predis> $redis The redis instance
     * @param string $key The key name to push records to
     * @param int $capSize Number of entries to limit list size to, 0 = unlimited
     */
    public function __construct(Predis $redis, string $key, int|string|Level $level = Level::Debug, bool $bubble = true, int $capSize = 0)
    {
        $this->redisClient = $redis;
        $this->redisKey = $key;
        $this->capSize = $capSize;

        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        if ($this->capSize > 0) {
            $this->writeCapped($record);
        } else {
            $this->redisClient->rpush($this->redisKey . '_' . $record->channel, [
                '[' . $record->datetime->format('H:i:s') . ']' . $record->message
            ]);
        }
        $this->redisClient->expire($this->redisKey, 3600);
    }

    /**
     * Write and cap the collection
     * Writes the record to the redis list and caps its
     */
    protected function writeCapped(LogRecord $record): void
    {
        $redisKey = $this->redisKey;
        $capSize = $this->capSize;
        $this->redisClient->transaction(function ($tx) use ($record, $redisKey, $capSize) {
            $tx->rpush($this->redisKey . '_' . $record->channel, [
                '[' . $record->datetime->format('H:i:s') . ']' . $record->message
            ]);
            $tx->ltrim($redisKey, -$capSize, -1);
            $tx->expire($redisKey, 3600);
        });
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }

    public function getLastLogs(string $key, int $count = 10): array
    {
        return $this->redisClient->lrange('logs_' . $key, -$count, -1);
    }
}