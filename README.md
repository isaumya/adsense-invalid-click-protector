# AdSense Invalid Click Protector (AICP) [![Version](https://img.shields.io/wordpress/plugin/v/ad-invalid-click-protector.svg?style=flat-square)](https://wordpress.org/plugins/ad-invalid-click-protector/) ![Downloads](https://img.shields.io/wordpress/plugin/dt/ad-invalid-click-protector.svg?style=flat-square) ![Rating](https://img.shields.io/wordpress/plugin/r/ad-invalid-click-protector.svg?style=flat-square)
One plugin to save your AdSense account from Click Bombings and Invalid Click Activities

> Just because in the name of the plugin there is "AdSense" doesn't mean that it only work with AdSense ads. You can literally use it with any ad media you want. Whether it is Google AdSense or some other publisher or even affiliate ads. As long as you follow the proper usage of the plugin mentioned below, it's gonna work just fine.

## Description

AdSense Invalid Click Protector a.k.a. AICP plugin will help you to save your Google AdSense account from unusual invalid click activities and click bombings. As per the Google AdSense terms, Google doesn't take any responsibility towards these invalid click activities or click bombings and always point the finger towards the AdSense plublisher, giving him/her all the blames.

But now, this will come to an end with this AdSense Invalid Click Protector WordPress plugin. With the help of this plugin, you can secure your AdSense account by making sure invalid click activities never happens to your website. It's now time to put an end to this AdSense invalid click or click bombing fiasco.

### Video Demonstration About the Plugin Usage

Before start using the plugin, I will highly recommend you to take a look at this video demostration where I've explained everything about this plugin.

[![Video Demonstration About the AICP Plugin Usage](https://i.imgur.com/gF30D7s.jpg)](https://www.youtube.com/watch?v=kKFrhtjjvzM?rel=0)

**For plugin support please post your your feedback and support questions to the [Plugin's Support Forum](https://wordpress.org/support/plugin/ad-invalid-click-protector) or in [Github Issue Tracker](https://github.com/isaumya/adsense-invalid-click-protector/issues).**

> It took countless hours to code, design, test and to do several bugfix to make this plugin a reality. If you enjoy this plugin and understand the huge effort I put into this, please consider **[donating some amount](https://goo.gl/6qrufe) (no matter how small)** for keeping aliave the development of this plugin. Thank you again for using my plugin. Also if you love using this plugin, I would really appiciate if you take 2 minutes out of your busy schedule to **[share your review](https://wordpress.org/support/plugin/ad-invalid-click-protector/reviews/)** about this plugin.

Features of the plugin include:

* Set maximum ad click limit
* Block any visitor if he exceeds the mentioned click limit
* Ban some countries from seeing the ads on your site
* Ability to see the list of banned user details from the WordPress admin section
* Ability to delete any banned IP one by one or in bulk approach
* Ability to search any IP within the banned IP list
* Admin dashboard widget to show the total number of banned users

## Some FAQs

**Q. How to use this plugin with your site?**

I know there are many WordPress plugin where you basically paste your AdSense code and it shows your ad at various position of your website. But unfortunately it is not humanly possible for me to check every single plugin of such out there or contact each plugin developer. Here I'm showing you how to incorporate the **AdSense Invalid Click Protector (AICP)** plugin with your website's ad code, so that both other plugin developers and normal users who use custom codes to show up their ads can take advantage of this.

To use the AdSense Invalid Click Protector plugin with your ad code you basically have to do 2 simple things.

1. Put a `if( aicp_can_see_ads() ) { /* return your ad code here */ }` block before returning your ad code to the front end
2. Wrap your ad code within a simple `div` tag like this `<div class="aicp">...your ad code goes here...</div>`

Personally I create various WordPress shortcodes for various ad units that I use on my personal website. It is extremely easity to create shortcodes for your ad units while taking the advantage of AdSense Invalid Click Protector Plugin. Let me show you how to create a WordPress shortcode very easily.

To create a shortcode the first thing you need to do is, go to the `functions.php` file of your theme or your child theme and at the end of yoru file put any of the following code.

If you are using a **PHP version < 5.3**, you can create a shortcode in the following way:

```php
add_shortcode( 'your_shortcode_name', 'your_shortcode_function_name' );
function your_shortcode_function_name() {
	if( aicp_can_see_ads() ) { // This part will show ads to your non-banned visitors
        $adCode = '<div class="aicp"><!-- Don\'t forget to add this div with aicp class -->
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- AdSense Responsive Ad Code -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-1234567890"
             data-ad-slot="0987654321"
             data-ad-format="auto"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        </div><!-- end of the aicp div -->';
        return $adCode;
    } else { // Anything within this part will be shown to your banned visitors or to the blocked country visitors
        return '<div class="error">You have been blocked from seeing ads.</div>';
    }
}
```

If you are using **PHP version >= 5.3**, you don't need to give a function name, instead you can take advantage of of anonymous function like this way:

```php
add_shortcode( 'your_shortcode_name', function() {
	if( aicp_can_see_ads() ) { // This part will show ads to your non-banned visitors
        $adCode = '<div class="aicp"><!-- Don\'t forget to add this div with aicp class -->
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- AdSense Responsive Ad Code -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-1234567890"
             data-ad-slot="0987654321"
             data-ad-format="auto"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        </div><!-- end of the aicp div -->';
        return $adCode;
    } else { // Anything within this part will be shown to your banned visitors or to the blocked country visitors
        return '<div class="error">You have been blocked from seeing ads.</div>';
    }
} );
```

**Please Note:** if you want, you can completely ignore the `else {}` block above in case you don't wanna show anything special to the blocked visitors.

**Q. How can I know what PHP version I am using?**

You can install the [WP Server Stats](https://wordpress.org/plugins/wp-server-stats/) plugin in your website which will show you many important information about your hosting environment including what PHP version you are currently using.

**Q. Can this plugin be used with other ad medias?**

Ofcourse it can. The only reason I put AdSense in the plugin name is bacuse most people use AdSense, but you can use it with any ad media including affiliate ads, as long as you are incoporating your ad code with the `if( aicp_can_see_ads() ) {}` block and wrap your ad code with the `<div class="aicp">....ad....code....</div>` block.

**Q. Will it help me from stop clicking on my own ads?**

No, you are not supposed to click on your own ads. If you want you can use adblock in your borswer to save yourself from accidental clicking on your own ads. 

## Languages

AdSense Invalid Click Protector plugin is 100% translation ready. Right now it only has the English translation in it but over time with the community help I hope this plugin will have many language in it's language directory.

## Support the Plugin by Donating

If you like this plugin please don't forget to write a review and if possible please [Donate some amount](http://donate.isaumya.com/) to keep the plugin and it's development alive.

## Screenshots

![AICP - Admin Dashboard Widget Screenshot](https://i.imgur.com/xibThJ2.jpg)

Admin Dashboard Widget

![AICP - General Settings Page Screenshot](https://i.imgur.com/gZqEsNK.jpg)

General Settings Page

![AICP - Banned User List Page Screenshot](https://i.imgur.com/Qtq9OwB.jpg)

Banned User List Page

## Installation

1. Within your WordPress Admin Panel, Go to Plugins > Add New
2. Search for AdSense Invalid Click Protector or AICP and Install it
3. Go to your admin dashboard and you will see the dashboard widget over there.
4. To change the settings of the AdSense Invalid Click Protector, head over to **AdSense Invalid Click Protector** > **General Settings** menu in you WordPress's left vertical menu

## Changelog
For the actual plugin changelog, please checkout the [WordPress Plugin's Changelog section](https://wordpress.org/plugins/ad-invalid-click-protector/changelog/). It is hard to update the same thing in two seperate places.
