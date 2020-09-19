<?php

namespace App\Helpers;

use App\Models\Role;
use App\Models\User;
use App\Models\Tag;
use Cache;
use Carbon\Carbon;
use GuzzleHttp\Command\Exception\CommandClientException;
use Illuminate\Support\Collection;
use Psr\Log\NullLogger;
use RestCord\DiscordClient;
use RestCord\Model\Channel\Channel;
use RestCord\Model\Guild\GuildMember;
use function Deployer\get;

class Discord
{
    /**
     * @var DiscordClient
     */
    public static $bot;

    /**
     * Return a instance of the main guild Bot if not exist.
     * @return DiscordClient
     */
    public static function getDiscordBot(): DiscordClient
    {
        if (self::$bot === null) {
            self::$bot = new DiscordClient([
                'token' => config('services.discord.bot_secret'),
                'tokenType' => 'Bot',
                'logger' => new NullLogger(),
            ]);
        }
        return self::$bot;
    }

    /**
     * @param int $guildId
     * @param int $memberId
     * @return mixed
     */
    public static function getGuildMember(int $memberId, int $guildId = null)
    {
        $guildId = $guildId ?? (int)config('services.discord.guild_id');
        $key = 'guild.' . $guildId . '.member.' . $memberId;
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        try {
            $member = collect(self::getDiscordBot()->guild->getGuildMember([
                'guild.id' => $guildId,
                'user.id' => $memberId
            ]));
        } catch (CommandClientException $e) {
            return null;
        }

        Cache::put($key, $member, now()->addHours(5));

        return self::getGuildMember($memberId, $guildId);
    }

    /**
     * Return the private channel id of a discord user.
     * @param User $user
     * @return Channel
     */
    public static function getDmChannel(User $user) : Channel
    {
        return self::getDiscordBot()->user->createDm(['recipient_id' => (int)$user->getKey()]);
    }

    /**
     * Get the default embed array.
     * @return array
     */
    public static function getDefaultEmbed(): array
    {
        return [
            'timestamp' => Carbon::now(),
            'color' => 13632027,
            'author' => [
                'name' => env('APP_NAME'),
                'icon_url' => asset('assets/img/favicon/favicon-96x96.png')
            ],
            'footer' => [
                'icon_url' => asset('assets/img/favicon/favicon-96x96.png'),
                'text' => env('APP_URL')
            ]
        ];
    }

    public static function createLog(string $title, string $text, $fields = [], $channel = 710813638671466517)
    {
        self::getDiscordBot()->channel->createMessage([
            'channel.id' => $channel,
            'embed' => [
                "timestamp" => Carbon::now(),
                "author" => [
                    "name" => "[Web Logs] » ".$title,
                    "icon_url" => "https://images-ext-1.discordapp.net/external/Dz5gfqQHC1SZKACxCWYcF1iPekolK-kwuE2uKIbepRE/%3Fsize%3D2048/https/cdn.discordapp.com/avatars/614510476256084165/4e5e0cdd9df766bc558c05c71484692c.png"
                ],
                "description" => $text,
                'fields' => $fields
            ],
        ]);
    }
}
