<?php

declare(strict_types=1);


namespace App\Shared;

final class ApplicationDateTime
{
    private const STORAGE_TZ = 'UTC';
    private const DEFAULT_INPUT_TZ = 'Asia/Tbilisi';

    public static function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone(self::STORAGE_TZ));
    }

    public static function nowLocal(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone(self::DEFAULT_INPUT_TZ));
    }

    public static function fromInput(
        string|\DateTimeInterface $value,
        ?string $inputTz = null
    ): \DateTimeImmutable {
        $tz = new \DateTimeZone($inputTz ?? self::DEFAULT_INPUT_TZ);

        if ($value instanceof \DateTimeInterface) {
            $dt = \DateTimeImmutable::createFromInterface($value);
        } else {
            $dt = new \DateTimeImmutable($value, $tz);
        }

        return $dt->setTimezone(new \DateTimeZone(self::STORAGE_TZ));
    }

    public static function toUserTz(
        \DateTimeInterface $value,
        ?string $userTz = null
    ): \DateTimeImmutable {
        $tz = new \DateTimeZone($userTz ?? self::DEFAULT_INPUT_TZ);

        return \DateTimeImmutable::createFromInterface($value)
            ->setTimezone($tz);
    }

    public static function toDb(\DateTimeInterface $value): string
    {
        return \DateTimeImmutable::createFromInterface($value)
            ->setTimezone(new \DateTimeZone(self::STORAGE_TZ))
            ->format('Y-m-d H:i:s');
    }

    public static function fromDb(string $value): \DateTimeImmutable
    {
        return new \DateTimeImmutable($value, new \DateTimeZone(self::STORAGE_TZ));
    }

    public static function fromTimestamp(int $ts): \DateTimeImmutable
    {
        return (new \DateTimeImmutable('@' . $ts))
            ->setTimezone(new \DateTimeZone(self::STORAGE_TZ));
    }

}
