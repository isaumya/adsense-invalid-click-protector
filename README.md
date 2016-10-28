# AdSense Invalid Click Protector (WordPress Plugin)
This is a WordPress plugin created to end the AdSense Invalid Clicks and unethical Click Bombings 

> Please note this plugin is currently in **_alpha_** mode and still being developed. So, it is not recommended to use this plugin on production environment as it may have many unsolved bugs in it. But if you want, feel free to try it out.

### Usage
There are two things you need to do to take advantage of the plugin. One task is at html side and the other on the PHP side. After installing and activating this plugin, wrap your AdSense codes within a div **with the exact class name** as shown bwlow:

```html
<div class="aicp">
[Your AdSense Code Here]
</div>
```
After this is done, you need to wrap the entire HTML code within this simple PHP checking

```php
<?php
if( aicp_can_see_ads() ) {
	echo '<div class="aicp">
	[Your AdSense Code Here]
	</div>';
} else {
	echo 'You have been blocked from seeing ads.';
}
```

if you don't wanna show any notice to the blocked users, you can get rid of the `else` block in the above code and can only use the `if` statement.


#### Creating Shortcodes for AdSense Ad Code with AICP benefits
If you are thinking how you can use this custom div or the extra PHP checking within at the time of showing your ads, one easiest solution is creating a simple WordPress shortcode for each of your ad unit and then use those shortcodes within your content to show up ads.

Here's an example code of creating a simple shortcode to show up ads

```php
<?php
add_shortcode( 'ad_unit_1', function() {
	if( aicp_can_see_ads() ) {
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
		</div>';
		return $adCode;
	} else {
		return 'You have been blocked from seeing ads.';
	}
} );
```
### Also Note
* This plugin can also be used for non-adsense codes too