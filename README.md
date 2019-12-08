# WooCommerce Admin - Notice Example

Adds a note to the merchant's inbox showing dummy text and two action buttons.

---

## What's This?

:warning: It's a simple example of creating your own notice for the new WooCommerce Admin dashboard. It's experimental as the WooCommerce Admin is still in development so this example may change overtime.

---

## Screenshot

![Screenshot of the example notice](https://raw.githubusercontent.com/seb86/wc-admin-notice-example/master/screenshot.png)

## Prerequisites

[WordPress 5.2 or greater](https://wordpress.org/download/) and [WooCommerce 3.6.0 or greater](https://wordpress.org/plugins/woocommerce/) should be installed prior to activating the WooCommerce Admin feature plugin and this example plugin.

## Dev Notes

In the plugin example you will find commented out lines that you may find useful to use to check things before displaying your notice. Simply uncomment the lines of code you wish to use before activating the plugin.

The plugin activates and runs after WooCommerce Admin has loaded to ensure proper loading order. The notice runs via the `wc_admin_daily` cron schedule which is once a day and `wc_admin_sunday` which is every week on Sunday. It may not show until the next scheduled event. Which ever comes first.

Overtime if you have left this plugin active with no modifications made you will see the notice will eventually be showing more than once. That is because of this line ```WC_Admin_Notes::delete_notes_with_name( self::NOTE_NAME );``` is disabled to demonstrate that each note is unique in the database. I would uncomment it when creating your own note.

You will also notice in the screenshot the icon is different. You can use any icon from the [GridIcons Collection](http://automattic.github.io/gridicons/). You will only need the name of the icon. The icon name I have used in this example is named `reader` to present the note as an article excerpt. The current default admin notes use the following icons: `info`, `product`, `thumbs-up`, `mail` and `mobile`.

You can have up to two action buttons. Depending if you are allowing the note to be snoozable, you can have up to three buttons. The action buttons also have two classes, primary which is the pink one and none primary which is the light grey. Which order you place the action buttons is up to you.

The buttons must link to a WordPress admin page or to an external URL. Leaving the query of the button blank or even using a hashtag `#` will simply redirect the user back to the WordPress Dashboard when the button is clicked on.

Here is the full code snippet of creating a note of your own with this example plugin. Notice the last two variables allow you to make the notice snoozable and set the language of the notice.

```php
self::create_new_note( array(
	'title'     => __( 'My Note Title', 'wc-admin-note-example' ),
	'content'   => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Iam enim adesse poterit. Prodest, inquit, mihi eo esse animo. <strong>Pollicetur certe.</strong> Duo Reges: constructio interrete.', 'wc-admin-note-example' ),
	'icon'      => 'reader',
	'note_name' => self::NOTE_NAME,
	'source'    => 'wc-admin-note-example',
	'actions'   => array(
		array(
			'name'    => 'do-something',
			'label'   => __( 'Click Me', 'wc-admin-note-example' ),
			'query'   => wc_admin_url(),
			'status'  => 'actioned',
			'primary' => false
		),
		array(
			'name'    => 'external-url',
			'label'   => __( 'View Repository', 'wc-admin-note-example' ),
			'query'   => 'https://github.com/seb86/wc-admin-notice-example',
			'status'  => 'actioned',
			'primary' => true
		)
	),
	'is_snoozable' => true,
	'locale'       => 'en_US'
) );
```

Have fun experimenting! 😄

## Important

* This is an example of creating your own note and not officially supported in any way.
* You must have WooCommerce Admin version 0.22.0 or above installed.
* Recommend that you experiement on a development environment only with this plugin.

## ⭐ Support

WooCommerce Admin - Notice Example is released freely and openly. Feedback and approaches to solving limitations in WooCommerce Admin - Notice Example is greatly appreciated.

I **do not offer support** for WooCommerce Admin - Notice Example. Please understand this is a non-commercial project. As such:

* Any development time for it is effectively being donated and is therefore, limited.
* Critical issues may not be resolved promptly.

#### 📝 Reporting Issues

If you think you have found a bug in the project or want to see more examples added, please [open a new issue](https://github.com/seb86/wp-admin-feedback-modal/issues/new) and I will do my best to help you out.

## 👍 Contribute

If you or your company use **WooCommerce Admin - Notice Example** or appreciate the work I’m doing in open source, please consider supporting me directly so I can continue maintaining it. Any contribution you make is a big help and is greatly appreciated.

Please also consider starring ✨ and sharing 👍 the project repository! This helps the project getting known and grow with the community. 🙏

I accept one-time donations and monthly via [BuyMeACoffee.com](https://www.buymeacoffee.com/sebastien)

* [My PayPal](https://www.paypal.me/codebreaker)
* [BuyMeACoffee.com](https://www.buymeacoffee.com/sebastien)
* Bitcoin (BTC): `3L4cU7VJsXBFckstfJdP2moaNhTHzVDkKQ`
* Ethereum (ETH): `0xc6a3C18cf11f5307bFa11F8BCBD51F355b6431cB`
* Litecoin (LTC): `MNNy3xBK8sM8t1YUA2iAwdi9wRvZp9yRoi`

Thank you for your support! 🙌

---

<p align="center">
	<img src="https://raw.githubusercontent.com/seb86/my-open-source-readme-template/master/a-sebastien-dumont-production.png" width="353">
</p>