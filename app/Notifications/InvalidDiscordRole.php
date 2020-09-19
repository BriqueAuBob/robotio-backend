<?php

namespace App\Notifications;

use App\Helpers\Discord;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class InvalidDiscordRole extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return [ DiscordChannel::class, 'database' ];
    }

    /**
     * Get the embed representation of the notification.
     * @param $notifiable
     * @return DiscordMessage
     */
    public function toDiscord($notifiable): DiscordMessage
    {
        $embed = Discord::getDefaultEmbed();
        $embed['title'] =  __('notifications.invalid_discord_role.title');
        $embed['description'] =  __('notifications.invalid_discord_role.description');
        $embed['color'] = 13632027;
        return DiscordMessage::create('', $embed);
    }

    /**
     * Get the array representation of the notification.
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'action' => '',
            'link' => '',
            'message' => __('notifications.invalid_discord_role.title')
        ];
    }
}
