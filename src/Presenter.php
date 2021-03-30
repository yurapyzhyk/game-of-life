<?php

class Presenter
{
    public static function display(array $universe, int $iteration)
    {
        echo PHP_EOL. "Generation: $iteration" . PHP_EOL;

        foreach ($universe as $row => $cells) {
            foreach ($cells as $cell) {
                if ($cell === true) {
                    echo '| O ';
                } else {
                    echo '|   ';
                }
            }
            echo '|' . PHP_EOL;
            foreach ($cells as $cell) {
                echo '----';
            }
            echo '-' . PHP_EOL;
        }
    }
}