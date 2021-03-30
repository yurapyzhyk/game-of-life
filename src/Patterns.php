<?php

class Patterns
{
    private const GLIDER_PATTERN = [[-1,0],[0,1],[1,-1],[1,0],[1,1]];
    private const NEIGHBOURS_PATTERN = [[-1,-1],[-1,0],[-1,1],[0,-1],[0,1],[1,-1],[1,0],[1,1]];

    public static function getPatternIndices(string $pattern): array
    {
        try {
            return constant('self::' . strtoupper($pattern) . '_PATTERN');
        } catch (\Throwable $e) {
            echo sprintf('Sorry, there is no implementation for %s pattern yet.', ucfirst($pattern));
            return [];
        }
    }
}