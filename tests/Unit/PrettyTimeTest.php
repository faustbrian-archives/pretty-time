<?php

declare(strict_types=1);

use Konceiver\PrettyTime\PrettyTime;

test('prettify milliseconds', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input))->toBe($output);
})->with([
    [0, '0ms'],
    [0.1, '1ms'],
    [1, '1ms'],
    [999, '999ms'],
    [1000, '1s'],
    [1000 + 400, '1.4s'],
    [(1000 * 2) + 400, '2.4s'],
    [1000 * 55, '55s'],
    [1000 * 67, '1m 7s'],
    [1000 * 60 * 5, '5m'],
    [1000 * 60 * 67, '1h 7m'],
    [1000 * 60 * 60 * 12, '12h'],
    [1000 * 60 * 60 * 40, '1d 16h'],
    [1000 * 60 * 60 * 999, '41d 15h'],
    [1000 * 60 * 60 * 24 * 465, '1y 100d'],
    [1000 * 60 * 67 * 24 * 465, '1y 154d 6h'],
    [119999, '1m 59.9s'],
    [120000, '2m'],
]);

test('have a compact option', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, ['compact' => true]))->toBe($output);
})->with([
    [1000 + 4, '1s'],
    [1000 * 60 * 60 * 999, '41d'],
    [1000 * 60 * 60 * 24 * 465, '1y'],
    [1000 * 60 * 67 * 24 * 465, '1y'],
]);

test('have a unitCount option', function ($input, $unitCount, $output): void {
    expect(PrettyTime::milliseconds($input, compact('unitCount')))->toBe($output);
})->with([
    [1000 * 60, 0, '1m'],
    [1000 * 60, 1, '1m'],
    [1000 * 60 * 67, 1, '1h'],
    [1000 * 60 * 67, 2, '1h 7m'],
    [1000 * 60 * 67 * 24 * 465, 1, '1y'],
    [1000 * 60 * 67 * 24 * 465, 2, '1y 154d'],
    [1000 * 60 * 67 * 24 * 465, 3, '1y 154d 6h'],
]);

test('have a secondsDecimalDigits option', function ($input, $options, $output): void {
    expect(PrettyTime::milliseconds($input, $options))->toBe($output);
})->with([
    [10000, [], '10s'],
    [33333, [], '33.3s'],
    [999, ['secondsDecimalDigits' => 0], '999ms'],
    [1000, ['secondsDecimalDigits' => 0], '1s'],
    [1999, ['secondsDecimalDigits' => 0], '1s'],
    [2000, ['secondsDecimalDigits' => 0], '2s'],
    [33333, ['secondsDecimalDigits' => 0], '33s'],
    [33333, ['secondsDecimalDigits' => 4], '33.3330s'],
]);

test('have a millisecondsDecimalDigits option', function ($input, $options, $output): void {
    expect(PrettyTime::milliseconds($input, $options))->toBe($output);
})->with([
    [33.333, [], '33ms'],
    [33.333, ['millisecondsDecimalDigits' => 0], '33ms'],
    [33.333, ['millisecondsDecimalDigits' => 4], '33.3330ms'],
]);

test('have a keepDecimalsOnWholeSeconds option', function ($input, $options, $output): void {
    expect(PrettyTime::milliseconds($input, $options))->toBe($output);
})->with([
    [1000 * 33, ['secondsDecimalDigits' => 2, 'keepDecimalsOnWholeSeconds' => true], '33.00s'],
    [1000 * 33.00004, ['secondsDecimalDigits' => 2, 'keepDecimalsOnWholeSeconds' => true], '33.00s'],
]);

test('have a verbose option', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, ['verbose' => true]))->toBe($output);
})->with([
    [0, '0 milliseconds'],
    [0.1, '1 millisecond'],
    [1, '1 millisecond'],
    [1000, '1 second'],
    [1000 + 400, '1.4 seconds'],
    [(1000 * 2) + 400, '2.4 seconds'],
    [1000 * 5, '5 seconds'],
    [1000 * 55, '55 seconds'],
    [1000 * 67, '1 minute 7 seconds'],
    [1000 * 60 * 5, '5 minutes'],
    [1000 * 60 * 67, '1 hour 7 minutes'],
    [1000 * 60 * 60 * 12, '12 hours'],
    [1000 * 60 * 60 * 40, '1 day 16 hours'],
    [1000 * 60 * 60 * 999, '41 days 15 hours'],
    [1000 * 60 * 60 * 24 * 465, '1 year 100 days'],
    [1000 * 60 * 67 * 24 * 465, '1 year 154 days 6 hours'],
]);

test('have a separateMilliseconds option', function ($input, $separateMilliseconds, $output): void {
    expect(PrettyTime::milliseconds($input, ['separateMilliseconds' => $separateMilliseconds]))->toBe($output);
})->with([
    [1100, false, '1.1s'],
    [1100, true, '1s 100ms'],
]);

test('have a formatSubMilliseconds option', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, ['formatSubMilliseconds' => true]))->toBe($output);
})->with([
    [0.4, '400µs'],
    [0.123571, '123µs 571ns'],
    [0.123456789, '123µs 456ns'],
    [(60 * 60 * 1000) + (23 * 1000) + 433 + 0.123456, '1h 23s 433ms 123µs 456ns'],
]);

test('work with verbose and compact options', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, [
        'verbose' => true,
        'compact' => true,
    ]))->toBe($output);
})->with([
    [1000, '1 second'],
    [1000 + 400, '1 second'],
    [(1000 * 2) + 400, '2 seconds'],
    [1000 * 5, '5 seconds'],
    [1000 * 55, '55 seconds'],
    [1000 * 67, '1 minute'],
    [1000 * 60 * 5, '5 minutes'],
    [1000 * 60 * 67, '1 hour'],
    [1000 * 60 * 60 * 12, '12 hours'],
    [1000 * 60 * 60 * 40, '1 day'],
    [1000 * 60 * 60 * 999, '41 days'],
    [1000 * 60 * 60 * 24 * 465, '1 year'],
    [1000 * 60 * 67 * 24 * 750, '2 years'],
]);

test('work with verbose and unitCount options', function ($input, $unitCount, $output): void {
    expect(PrettyTime::milliseconds($input, ['verbose' => true, 'unitCount' => $unitCount]))->toBe($output);
})->with([
    [1000 * 60, 1, '1 minute'],
    [1000 * 60 * 67, 1, '1 hour'],
    [1000 * 60 * 67, 2, '1 hour 7 minutes'],
    [1000 * 60 * 67 * 24 * 465, 1, '1 year'],
    [1000 * 60 * 67 * 24 * 465, 2, '1 year 154 days'],
    [1000 * 60 * 67 * 24 * 465, 3, '1 year 154 days 6 hours'],
]);

test('work with verbose and secondsDecimalDigits options', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, [
        'verbose'              => true,
        'secondsDecimalDigits' => 4,
    ]))->toBe($output);
})->with([
    [1000, '1 second'],
    [1000 + 400, '1.4000 seconds'],
    [(1000 * 2) + 400, '2.4000 seconds'],
    [(1000 * 5) + 254, '5.2540 seconds'],
    [33333, '33.3330 seconds'],
]);

test('work with verbose and millisecondsDecimalDigits options', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, [
        'verbose'                   => true,
        'millisecondsDecimalDigits' => 4,
    ]))->toBe($output);
})->with([
    [1, '1.0000 millisecond'],
    [1 + 0.4, '1.4000 milliseconds'],
    [(1 * 2) + 0.4, '2.4000 milliseconds'],
    [(1 * 5) + 0.254, '5.2540 milliseconds'],
    [33.333, '33.3330 milliseconds'],
]);

test('work with verbose and formatSubMilliseconds options', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, ['formatSubMilliseconds' => true, 'verbose' => true]))->toBe($output);
})->with([
    [0.4, '400 microseconds'],
    [0.123571, '123 microseconds 571 nanoseconds'],
    [0.123456789, '123 microseconds 456 nanoseconds'],
    [0.001, '1 microsecond'],
]);

test('compact option overrides unitCount option', function ($unitCount): void {
    expect(PrettyTime::milliseconds(1000 * 60 * 67 * 24 * 465, ['verbose' => true, 'compact' => true, 'unitCount' => $unitCount]))->toBe('1 year');
})->with([1, 2, 3]);

test('work with separateMilliseconds and formatSubMilliseconds options', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, [
        'separateMilliseconds'  => true,
        'formatSubMilliseconds' => true,
    ]))->toBe($output);
})->with([
    [1010.340067, '1s 10ms 340µs 67ns'],
    [(60 * 1000) + 34 + 0.000005, '1m 34ms 5ns'],
]);

test('throw on invalid', function (): void {
    PrettyTime::milliseconds(INF);
})->throws(InvalidArgumentException::class, 'Expected a finite number');

test('properly rounds milliseconds with secondsDecimalDigits', function ($input, $output): void {
    expect(PrettyTime::milliseconds($input, [
        'verbose'              => true,
        'secondsDecimalDigits' => 0,
    ]))->toBe($output);
})->with([
    [3 * 60 * 1000, '3 minutes'],
    [(3 * 60 * 1000) - 1, '2 minutes 59 seconds'],
    [365 * 24 * 3600 * 1e3, '1 year'],
    [(365 * 24 * 3600 * 1e3) - 1, '364 days 23 hours 59 minutes 59 seconds'],
    [24 * 3600 * 1e3, '1 day'],
    [(24 * 3600 * 1e3) - 1, '23 hours 59 minutes 59 seconds'],
    [3600 * 1e3, '1 hour'],
    [(3600 * 1e3) - 1, '59 minutes 59 seconds'],
    [2 * 3600 * 1e3, '2 hours'],
    [(2 * 3600 * 1e3) - 1, '1 hour 59 minutes 59 seconds'],
]);

test('`colonNotation` option', function ($input, $options, $output): void {
    expect(PrettyTime::milliseconds($input, $options))->toBe($output);
})->with([
    // Default formats
    [1000, ['colonNotation' => true], '0:01'],
    [1543, ['colonNotation' => true], '0:01.5'],
    [1000 * 60, ['colonNotation' => true], '1:00'],
    [1000 * 90, ['colonNotation' => true], '1:30'],
    [95543, ['colonNotation' => true], '1:35.5'],
    [(1000 * 60 * 10) + 543, ['colonNotation' => true], '10:00.5'],
    [(1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true], '59:59.5'],
    [(1000 * 60 * 60 * 15) + (1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true], '15:59:59.5'],
    // Together with `secondsDecimalDigits`
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 0], '0:00'],
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 1], '0:00.9'],
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 2], '0:00.99'],
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '0:00.999'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 0], '0:01'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 1], '0:01'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 2], '0:01'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '0:01'],
    [1001, ['colonNotation' => true, 'secondsDecimalDigits' => 0], '0:01'],
    [1001, ['colonNotation' => true, 'secondsDecimalDigits' => 1], '0:01'],
    [1001, ['colonNotation' => true, 'secondsDecimalDigits' => 2], '0:01'],
    [1001, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '0:01.001'],
    [1543, ['colonNotation' => true, 'secondsDecimalDigits' => 0], '0:01'],
    [1543, ['colonNotation' => true, 'secondsDecimalDigits' => 1], '0:01.5'],
    [1543, ['colonNotation' => true, 'secondsDecimalDigits' => 2], '0:01.54'],
    [1543, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '0:01.543'],
    [95543, ['colonNotation' => true, 'secondsDecimalDigits' => 0], '1:35'],
    [95543, ['colonNotation' => true, 'secondsDecimalDigits' => 1], '1:35.5'],
    [95543, ['colonNotation' => true, 'secondsDecimalDigits' => 2], '1:35.54'],
    [95543, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '1:35.543'],
    [(1000 * 60 * 10) + 543, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '10:00.543'],
    [(1000 * 60 * 60 * 15) + (1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true, 'secondsDecimalDigits' => 3], '15:59:59.543'],
    // Together with `keepDecimalsOnWholeSeconds`
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 0, 'keepDecimalsOnWholeSeconds' => true], '0:00'],
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 1, 'keepDecimalsOnWholeSeconds' => true], '0:00.9'],
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 2, 'keepDecimalsOnWholeSeconds' => true], '0:00.99'],
    [999, ['colonNotation' => true, 'secondsDecimalDigits' => 3, 'keepDecimalsOnWholeSeconds' => true], '0:00.999'],
    [1000, ['colonNotation' => true, 'keepDecimalsOnWholeSeconds' => true], '0:01.0'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 0, 'keepDecimalsOnWholeSeconds' => true], '0:01'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 1, 'keepDecimalsOnWholeSeconds' => true], '0:01.0'],
    [1000, ['colonNotation' => true, 'secondsDecimalDigits' => 3, 'keepDecimalsOnWholeSeconds' => true], '0:01.000'],
    [1000 * 90, ['colonNotation' => true, 'keepDecimalsOnWholeSeconds' => true], '1:30.0'],
    [1000 * 90, ['colonNotation' => true, 'secondsDecimalDigits' => 3, 'keepDecimalsOnWholeSeconds' => true], '1:30.000'],
    [1000 * 60 * 10, ['colonNotation' => true, 'secondsDecimalDigits' => 3, 'keepDecimalsOnWholeSeconds' => true], '10:00.000'],
    // Together with `unitCount`
    [1000 * 90, ['colonNotation' => true, 'secondsDecimalDigits' => 0, 'unitCount' => 1], '1'],
    [1000 * 90, ['colonNotation' => true, 'secondsDecimalDigits' => 0, 'unitCount' => 2], '1:30'],
    [1000 * 60 * 90, ['colonNotation' => true, 'secondsDecimalDigits' => 0, 'unitCount' => 3], '1:30:00'],
    [95543, ['colonNotation' => true, 'secondsDecimalDigits' => 1, 'unitCount' => 1], '1'],
    [95543, ['colonNotation' => true, 'secondsDecimalDigits' => 1, 'unitCount' => 2], '1:35.5'],
    [95543 + (1000 * 60 * 60), ['colonNotation' => true, 'secondsDecimalDigits' => 1, 'unitCount' => 3], '1:01:35.5'],
    // Make sure incompatible options fall back to `'colonNotation'`
    [(1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true, 'formatSubMilliseconds' => true], '59:59.5'],
    [(1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true, 'separateMilliseconds' => true], '59:59.5'],
    [(1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true, 'verbose' => true], '59:59.5'],
    [(1000 * 60 * 59) + (1000 * 59) + 543, ['colonNotation' => true, 'compact' => true], '59:59.5'],
]);
