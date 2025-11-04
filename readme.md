# Direktt Auto Greet

A powerful WordPress plugin for sending automated messages, tightly integrated with the [Direktt WordPress Plugin](https://direktt.com/).

- **Send customizable notifications** to users and admins when new user subscribes to the channel.
- **Send customizable notifications** to users if you are out off office at current time.

## Requirements

- WordPress 5.0 or higher
- The [Direktt Plugin](https://wordpress.org/plugins/direktt/) (must be active)

## Installation

1. Install and activate the **Direktt** core plugin.
2. Download the direktt-auto-greet.zip from the latest [release](https://github.com/direktt/direktt-auto-greet/releases)
2. Upload **direktt-auto-greet.zip** either through WordPress' **Plugins > Add Plugin > Upload Plugin** or upload the contents of this direktt-auto-greet.zip to the `/wp-content/plugins/` directory of your WordPress installation.
3. Activate **Direktt Auto Greet** from your WordPress plugins page.
4. Configure the plugin under **Direktt > Settings > Auto Greet Settings**.

## Usage

### Admin Interface

- Find **Direktt > Settings > Auto Greet Settings** in your WordPress admin menu.
- Configure:
    - Do you want to send Welcome message for new subscribers?
    - Choose the notification which will be sent to new subscribers.
    - Do you want to send a notification to Direktt Admin when new user subscribes?
    - Choose the notification which will be sent to Direktt Admin when new user subscribes.
    - Set up Out of Office Auto Responder mode (Off/Only During Non-working hours/Always on)
    - Set up your working hours.
    - Choose the notification which will be sent to user for Auto Reponder Always on mode.
    - Choose the notification which will be sent to user for Auto Reponder Non-working hours mode.

### Workflow

- Welcome:
    - User subscribes to the channel:
        - If "New Subscribers" checkbox is checked and "Subscriber Message Template" is configured, "Subscriber Message Template" message will be sent to that new subscriber.
        - If "Admin" checkbox is checked and "Admin Message Template" is configured, "Admin Message Template" message will be sent to the Direktt Admin.
- Out of Office Auto Responder:
    - Off - There won't be Automated Messages (this does not affect Welcome messages).
    - Only During Non-working hours:
        - Example - Subscriber sends a message at Thursday, 7:30 PM, Working hours for Thursday are 9:00 AM - 5:00 PM. "Non-working hours mode message template" message will be sent to user.  
        - Example - Subscriber sends a message at Sunday, Sunday is configured as closed. "Non-working hours mode message template" message will be sent to user.  
        - Example - Subscriber sends a message at Monday, 2:00 PM, Working hours for Monday are 9:00 AM - 5:00 PM. Nothing will be sent to user.  
    - Always on:
        - Example - You go on holiday and set Out of Office Auto Responder mode to Always On. Subscriber sends a message to you, and they will get the automated "Non-working hours mode message template" message.

### Admin Shortcode (Front End)

Intended only for Direktt Admin (no other user can see it), with this Shortcode Direktt Admin can change the current Out of Office Auto Responder mode:

```[direktt_auto_greet]```

## Notification Templates

Direktt Message templates support following dynamic placeholders:

- `#title#` — display name of the new subsriber (only for welcome messages)
- `#subscriptionId#` — subscription id of the new subscriber (only for welcome messages)

## Updating

The plugin supports updating directly from this GitHub repository.

---

## License

GPL-2.0-or-later

---

## Support

Contact [Direktt](https://direktt.com/) for questions, issues, or contributions.