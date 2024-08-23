<?php

declare(strict_types=1);

namespace App\Service\Game;

class Move
{
    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function __construct(private array $from, private array $to) {}

    /**
     * @return array<int>
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @param array<int> $from
     */
    public function setFrom(array $from): void
    {
        $this->from = $from;
    }

    /**
     * @return array<int>
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param array<int> $to
     */
    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    public static function createMoveWithCellTransform(
        string $cellFrom,
        string $cellTo,
    ): self {
        $inputTransformer = new InputTransformer();
        [$cellFromTransformed, $cellToTransformed] = $inputTransformer->transformInputToArray($cellFrom, $cellTo);
        if (!is_array($cellFromTransformed) || !is_array($cellToTransformed)) {
            throw new \RuntimeException('Cells are no transformed');
        }

        if ($cellFromTransformed === [] || $cellToTransformed === []) {
            throw new \RuntimeException('Cells are empty');
        }

        return new self($cellFromTransformed, $cellToTransformed);
    }
}
