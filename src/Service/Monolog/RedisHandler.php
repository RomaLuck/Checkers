<?php

declare(strict_types=1);

namespace App\Service\Monolog;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Predis\Client as Predis;

final class RedisHandler extends AbstractProcessingHandler
{
    protected int $capSize;
    /** @var Predis<Predis> */
    private Predis $redisClient;

    private string $redisKey;

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
     * @return array<string>
     */
    public function getLastLogs(string $key, int $count = 10): array
    {
        return $this->redisClient->lrange('logs_' . $key, -$count, -1);
    }

    protected function write(LogRecord $record): void
    {
        if ($this->capSize > 0) {
            $this->writeCapped($record);
        } else {
            $this->redisClient->rpush($this->redisKey . '_' . $record->channel, [
                '[' . $record->datetime->format('H:i:s') . ']' . $record->message,
            ]);
        }
        $this->redisClient->expire($this->redisKey . '_' . $record->channel, 3600);
    }

    /**
     * Write and cap the collection
     * Writes the record to the redis list and caps its
     */
    protected function writeCapped(LogRecord $record): void
    {
        $redisKey = $this->redisKey;
        $capSize = $this->capSize;
        $channel = $record->channel;
        $this->redisClient->transaction(static function ($tx) use ($record, $redisKey, $capSize, $channel): void {
            $tx->rpush($redisKey . '_' . $channel, [
                '[' . $record->datetime->format('H:i:s') . ']' . $record->message,
            ]);
            $tx->ltrim($redisKey . '_' . $channel, -$capSize, -1);
            $tx->expire($redisKey . '_' . $channel, 3600);
        });
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }
}
