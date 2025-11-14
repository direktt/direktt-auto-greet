# Direktt Auto Greet

Direktt Auto Greet is a WordPress plugin designed for businesses using the [Direktt mobile app](https://direktt.com) to connect with their clients. 

Seamlessly integrated with the Direktt chat-based platform, this extension automatically sends personalized welcome messages to new subscribers and handles out-of-office replies—making your client communication effortless, timely, and always professional.  
Perfect for busy SMEs looking to streamline conversations and deliver a polished experience with zero hassle.  
  
It is tightly integrated with the [Direktt WordPress Plugin](https://wordpress.org/plugins/direktt/).  

With Direktt Auto Greet you can:

- **Send customizable notifications** to users and channel admins when new user subscribes to the channel.
- **Send customizable notifications** to users if you are out off office at current time.

## Documentation

You can find the detailed plugin documentation, guides and tutorials in the Wiki section:  
https://github.com/direktt/direktt-auto-greet/wiki 

## Requirements

- WordPress 5.6 or higher
- The [Direktt Plugin](https://wordpress.org/plugins/direktt/) (must be active)

## Installation

1. Install and activate the **Direktt** core plugin.
2. Download the direktt-auto-greet.zip from the latest [release](https://github.com/direktt/direktt-auto-greet/releases)
2. Upload **direktt-auto-greet.zip** either through WordPress' **Plugins > Add Plugin > Upload Plugin** or upload the contents of this direktt-auto-greet.zip to the `/wp-content/plugins/` directory of your WordPress installation.
3. Activate **Direktt Auto Greet** from your WordPress plugins page.
4. Configure the plugin under **Direktt > Settings > Auto Greet Settings**.

## Usage

### Plugin Settings

- Find **Direktt > Settings > Auto Greet Settings** in your WordPress admin menu.
- Configure:
    - Do you want to send Welcome message for new subscribers.
    - Choose the message template which will be sent to new subscribers.
    - Do you want to send a notification to Direktt Admin when new user subscribes.
    - Choose the message template which will be sent to Direktt Admin when new user subscribes.
    - Set up Out of Office Auto Responder mode (Off / Only During Non-working hours / Always on)
    - If Always on mode is selected, choose the message template which will be sent to user for Auto Reponder.
    - If Non-working hours mode is selected, choose the message template which will be sent to user for Auto Reponder and configure working hours.

### Workflow

- **Welcome**:
    - User subscribes to the channel:
        - If **"New Subscribers"** checkbox is checked and **"Subscriber Message Template"** is configured, "Subscriber Message Template" message will be sent to that new subscriber.
        - If **"Admin"** checkbox is checked and **"Admin Message Template"** is configured, "Admin Message Template" message will be sent to the Direktt Admin.
- **Out of Office Auto Responder**:
    - **Off** - There won't be Automated Messages (this does not affect Welcome messages).
    - **Only During Non-working hours**:
        - Example - Out of Office Auto Responder is set to **During non-working hours**. Subscriber sends a message at 7:30 PM Thursday. Working hours for Thursday are 9:00 AM - 5:00 PM. **"Non-working hours mode message template"** message will be sent to user.  
        - Example - Out of Office Auto Responder is set to **During non-working hours**. Subscriber sends a message on Sunday. Sunday is configured as closed. **"Non-working hours mode message template"** message will be sent to user.  
        - Example - Out of Office Auto Responder is set to **During non-working hours**. Subscriber sends a message at 2:00 PM on Monday. Working hours for Monday are 9:00 AM - 5:00 PM. No message will be sent to user.  
    - **Always on**:
        - Example - You go on holiday and set Out of Office Auto Responder mode to **Always On**. Subscriber sends a message to you, and gets the automated **"Always on mode message template"** message.

### Admin Shortcode (Front End)

```[direktt_auto_greet]```

Intended for Direktt channel admins. It enables Direktt channel admin to manage current Out of Office Auto Responder mode right from Direktt mobile app.

## Notification Templates

Direktt Message templates support following dynamic placeholders:

- `#display_name#` — display name of the new subsriber **(only for welcome messages)**
- `#subscription_id#` — subscription id of the new subscriber **(only for welcome messages)**

## Updating

The plugin supports updates directly from WordPress admin console.  

You can find all plugin releases in the Releases section of this repository:  
https://github.com/direktt/direktt-auto-greet/releases.

---

## License

GPL-2.0-or-later

---

## Support

Please use Issues section of this repository for any issue you might have:  
https://github.com/direktt/direktt-auto-greet/issues.

Join Direktt Community on Discord - [Direktt Discord Server](https://discord.gg/xaFWtbpkWp)  

Contact [Direktt](https://direktt.com/) for general questions, issues, or contributions.
