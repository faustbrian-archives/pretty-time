<?php

declare(strict_types=1);

/*
 * This file is part of Pretty time.
 *
 * (c) Konceiver Oy <info@konceiver.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Konceiver\PrettyTime;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class PrettyTime
{
    const SECOND_ROUNDING_EPSILON = 0.0000001;

    public static function milliseconds($milliseconds, array $options = []): string
    {
        if (! is_finite($milliseconds)) {
            throw new InvalidArgumentException('Expected a finite number');
        }

        if (! empty($options['colonNotation'])) {
            $options['compact']               = false;
            $options['formatSubMilliseconds'] = false;
            $options['separateMilliseconds']  = false;
            $options['verbose']               = false;
        }

        if (! empty($options['compact'])) {
            $options['secondsDecimalDigits']      = 0;
            $options['millisecondsDecimalDigits'] = 0;
        }

        $result = [];

        $parsed = static::parseMilliseconds($milliseconds);

        static::add($result, $options, explode('.', (string) ($parsed['days'] / 365))[0], 'year', 'y');
        static::add($result, $options, $parsed['days'] % 365, 'day', 'd');
        static::add($result, $options, $parsed['hours'], 'hour', 'h');
        static::add($result, $options, $parsed['minutes'], 'minute', 'm');

        if (
            ! empty($options['separateMilliseconds']) ||
            ! empty($options['formatSubMilliseconds']) ||
            (empty($options['colonNotation']) && $milliseconds < 1000)
        ) {
            static::add($result, $options, $parsed['seconds'], 'second', 's');

            if (! empty($options['formatSubMilliseconds'])) {
                static::add($result, $options, $parsed['milliseconds'], 'millisecond', 'ms');
                static::add($result, $options, $parsed['microseconds'], 'microsecond', 'µs');
                static::add($result, $options, $parsed['nanoseconds'], 'nanosecond', 'ns');
            } else {
                $millisecondsAndBelow =
                    $parsed['milliseconds'] +
                    ($parsed['microseconds'] / 1000) +
                    ($parsed['nanoseconds'] / 1e6);

                $millisecondsDecimalDigits = Arr::get($options, 'millisecondsDecimalDigits', 0);

                $roundedMiliseconds = $millisecondsAndBelow >= 1 ?
                    round($millisecondsAndBelow) :
                    ceil($millisecondsAndBelow);

                $millisecondsString = $millisecondsDecimalDigits ?
                    number_format(round($millisecondsAndBelow, $millisecondsDecimalDigits), $millisecondsDecimalDigits) :
                    $roundedMiliseconds;

                static::add(
                    $result,
                    $options,
                    floatval($millisecondsString), // 10
                    'millisecond',
                    'ms',
                    $millisecondsString
                );
            }
        } else {
            $seconds              = fmod($milliseconds / 1000, 60);
            $secondsDecimalDigits = Arr::get($options, 'secondsDecimalDigits', 1);
            $secondsFixed         = static::floorDecimals($seconds, $secondsDecimalDigits);
            $secondsString        = Arr::get($options, 'keepDecimalsOnWholeSeconds')
                ? $secondsFixed
                : preg_replace('/\.0+$/', '', $secondsFixed);

            static::add($result, $options, floatval($secondsString), 'second', 's', $secondsString);
        }

        if (count($result) === 0) {
            return '0'.(! empty($options['verbose']) ? ' milliseconds' : 'ms');
        }

        if (! empty($options['compact'])) {
            return $result[0];
        }

        if (Arr::get($options, 'unitCount')) {
            $separator = Arr::has($options, 'colonNotation') ? '' : ' ';

            return implode($separator, array_slice($result, 0, max($options['unitCount'], 1)));
        }

        return ! empty($options['colonNotation']) ? implode('', $result) : implode(' ', $result);
    }

    private static function floorDecimals($value, $decimalDigits)
    {
        $flooredInterimValue = floor(($value * (10 ** $decimalDigits)) + static::SECOND_ROUNDING_EPSILON);
        $flooredValue        = round($flooredInterimValue) / (10 ** $decimalDigits);

        return number_format(round($flooredValue, $decimalDigits), $decimalDigits);
    }

    private static function add(array &$result, $options, $value, $long, $short, $valueString = null)
    {
        $a = count($result) === 0 || empty($options['colonNotation']);
        $b = ($value === 0 || $value === 0.0 || $value === '0') && ! (Arr::get($options, 'colonNotation') && $short === 'm');

        if ($a && $b) {
            return;
        }

        $valueString = $valueString ?? $value ?? '0';
        $prefix      = '';
        $suffix      = '';
        if (! empty($options['colonNotation'])) {
            $prefix      = count($result) > 0 ? ':' : '';
            $suffix      = '';
            $wholeDigits = Str::contains($valueString, '.') ? strlen((string) explode('.', $valueString)[0]) : strlen((string) $valueString);
            $minLength   = count($result) > 0 ? 2 : 1;
            $valueString = str_repeat('0', max(0, $minLength - $wholeDigits)).$valueString;
        } else {
            $prefix = '';
            $suffix = ! empty($options['verbose']) ? ' '.(($value === '1' || $value === 1 || $value === 1.0) ? $long : $long.'s') : $short;
        }

        $result[] = sprintf('%s%s%s', $prefix, $valueString, $suffix);
    }

    private static function parseMilliseconds($milliseconds)
    {
        $roundTowardsZero = $milliseconds > 0
            ? fn ($value) => floor($value)
            : fn ($value) => ceil($value);

        return [
            'days'         => $roundTowardsZero($milliseconds / 86400000),
            'hours'        => fmod($roundTowardsZero($milliseconds / 3600000), 24),
            'minutes'      => fmod($roundTowardsZero($milliseconds / 60000), 60),
            'seconds'      => fmod($roundTowardsZero($milliseconds / 1000), 60),
            'milliseconds' => fmod($roundTowardsZero($milliseconds), 1000),
            'microseconds' => fmod($roundTowardsZero($milliseconds * 1000), 1000),
            'nanoseconds'  => fmod($roundTowardsZero($milliseconds * 1e6), 1000),
        ];
    }
}
