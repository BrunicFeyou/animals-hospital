<?php 

namespace App\Enum;

enum AppointmentStateEnum: string
{
    case programmed = 'Programmé';
    case onGoing = 'En cours';
    case done = 'Terminé';

    public static function fromValue(string $value): string
    {
        foreach (self::cases() as $status) {
            if( $value === $status->value ){
                return $status->name;
            }
        }
        throw new \ValueError("$value is not a valid backing value for enum " . self::class );
    }
}



