<?php

namespace Model\Sms;

/**
 * Class Template
 * @package Model\Sms
 */
class template extends Model
{
    const TAG_LIDS_BEFORE_CONSULT_DAY = 'lids_before_consult_day';
    const TAG_LIDS_BEFORE_CONSULT_HOUR = 'lids_before_consult_hour';

    /**
     * @var array|string[]
     */
    protected static array $messages = [
        self::TAG_LIDS_BEFORE_CONSULT_DAY => 'Приветствуем, завтра ждём к нам на открытый урок по музыке, как и договаривались. Если вдруг накладки - сообщите, ведь мы специально забронировали за вами время)',
        self::TAG_LIDS_BEFORE_CONSULT_HOUR => 'До встречи в школе музыки осталось совсем чуть-чуть, контрольное напоминание;)'
    ];

    /**
     * @param string $tag
     * @return template
     */
    public static function getByTag(string $tag) : template
    {
        return (new self())->tag($tag)->text(self::$messages[$tag]);
    }

}