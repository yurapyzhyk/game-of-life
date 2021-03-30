<?php

require './src/Patterns.php';
require './src/Presenter.php';

class Universe
{
    private $liveCells;

    private $dimension;

    private $universe;

    /**
     * Universe constructor.
     * @param $dimension
     */
    public function __construct($dimension)
    {
        $this->dimension = $dimension;
        $this->universe = [];

        $this->liveCells = [];
    }

    public function initialize(?string $pattern = null, ?int $anchorX = null, ?int $anchorY = null): void
    {
        for ($i = 0; $i < $this->dimension; $i++) {
            $this->universe[$i] = [];

            for ($j = 0; $j < $this->dimension; $j++) {
                $this->setDead($i, $j);
            }
        }

        if (!empty($pattern)) {
            $this->initializePattern($pattern, $anchorX, $anchorY);
        }
    }

    public function start(bool $silentMode = false): void
    {
        $i = 0;

        try {
            while (++$i) {
                if (!$silentMode) {
                    Presenter::display($this->universe, $i);
                }

                $this->nextGeneration();
                usleep(500000);
            }
        } catch (\Throwable $e) {
            echo PHP_EOL . "Game finished after $i generations" . PHP_EOL;
        }
    }

    private function nextGeneration(): void
    {
        $start = microtime(true);

        $result = $this->analyzeNeighbours();

        if (empty($result['dead']) && empty($result['reproduced'])) {
            throw new Exception('No more generations');
        }

        foreach ($result['dead'] as [$x, $y]) {
            $this->setDead($x, $y);
        }

        foreach ($result['reproduced'] as [$x, $y]) {
            $this->setLive($x, $y);
        }

        $end = microtime(true);

        $duration = number_format(($end - $start) * 1000, 3);

        echo "Generation calculated in {$duration}ms" . PHP_EOL;
    }

    private function analyzeNeighbours(): array
    {
        $dead = [];
        $reproduced = [];

        foreach ($this->liveCells as $x => $columns) {
            foreach (array_keys($columns) as $y) {
                $neighbours = $this->getNeighbours($x, $y);
                $totalLiveNeighbours = count($neighbours['live']);

                if ($totalLiveNeighbours < 2 || $totalLiveNeighbours > 3) {
                    array_push($dead, [$x, $y]);
                }

                $reproduced += $this->getReproduced($neighbours['dead']);
            }
        }

        return [
            'dead' => $dead,
            'reproduced' => $reproduced,
        ];
    }

    private function getReproduced(array $deadNeighbours): array
    {
        $reproduced = [];

        foreach ($deadNeighbours as [$x, $y]) {
            $neighbours = $this->getNeighbours($x, $y);
            $totalLiveNeighbours = count($neighbours['live']);

            if ($totalLiveNeighbours === 3) {
                array_push($reproduced, [$x, $y]);
            }
        }

        return $reproduced;
    }

    private function initializePattern(string $pattern, ?int $anchorX, ?int $anchorY): void
    {
        $center = ceil($this->dimension / 2) - 1;

        $anchorX = $anchorX ?? $center;
        $anchorY = $anchorY ?? $center;

        $patternIndices = Patterns::getPatternIndices($pattern);

        foreach ($patternIndices as [$offsetX, $offsetY]) {
            $x = $anchorX + $offsetX;
            $y = $anchorY + $offsetY;

            $this->setLive($x, $y);
        }
    }

    private function setLive(int $x, int $y): void
    {
        $this->universe[$x][$y] = true;

        if (!isset($this->liveCells[$x])) {
            $this->liveCells[$x] = [];
        }

        $this->liveCells[$x][$y] = true;
    }

    private function setDead(int $x, int $y): void
    {
        $this->universe[$x][$y] = false;

        if (isset($this->liveCells[$x], $this->liveCells[$x][$y])) {
            unset($this->liveCells[$x][$y]);
        }
    }

    private function getNeighbours(int $anchorX, int $anchorY): array
    {
        $neighboursIndices = Patterns::getPatternIndices('neighbours');

        $neighbours = [
            'live' => [],
            'dead' => [],
        ];

        foreach ($neighboursIndices as [$offsetX, $offsetY]) {
            $x = $anchorX + $offsetX;
            $y = $anchorY + $offsetY;

            if (isset($this->universe[$x], $this->universe[$x][$y])) {
                $state = $this->universe[$x][$y] ? 'live' : 'dead';
                array_push($neighbours[$state], [$x, $y]);
            }
        }

        return $neighbours;
    }
}