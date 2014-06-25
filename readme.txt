=== Perfect OneDrive Gallery & File ===
Contributors: perfect-web
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CDDZ6G6LDV4LG
Tags: onedrive, one drive, skydrive, microsoft, gallery, download, cloud storage
Requires at least: 2.8.0
Tested up to: 3.9.1
Stable tag: 1.0.5
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Share easily your photos and files stored on Microsoft OneDrive. You can display a gallery with your photos or a link to a file for download.

== Description ==

**Warning! OneDrvie plugin is not working with Windwos Live Applications created after 1st of June 2014. We are waiting for update of Microsoft OneDrive API documentation to make necessary changes in our software.**


![OneDrive](http://www.perfect-web.co/media/com_skydrive/images/onedrive-logo.png)

OneDrive Gallery & File plugin has been inspired by Microsoft. It integrates WordPress with OneDrive (SkyDrive). This plugin displays a photo gallery directly from folder on OneDrive. Each time you add more photos on your OneDrive they automatically apear on your WordPress website. You can also insert a single photo or a download link with a file form OneDrive. Then you can edit your file on OneDrive and be sure that documents on you WordPress website are always up to date.


= Features =

* Displays gallery of photos stored in folder on OneDrive.
* Displays each photo in Lightbox with description set on OneDrive.
* Displays a file from OneDrive as link for download with icon and size.
* Displays a photo inside post content.
* Use prettyPhoto jQuery for Lightbox effect.
* Insert short code into post with WYSIWYG editor button.
* Compatible with **Azure - Microsoft's Cloud Platform**.


= Front-end demo =

[Visit demo page](http://www.perfect-web.co/wordpress/microsoft-onedrive-gallery-file#page-demo)


= Microsoft Openness =

This plugin is brought to you thanks to new Microsoft attiude towards Open Source. Due to Microsoft help and support we were able to create such a bridge between one of Mirosoft leading projects and WordPress. We hope that you will find this plugin useful.

![Inspired by Microsoft Openness](http://www.perfect-web.co/media/com_skydrive/images/MSFT_logo.png)


= Translations =

English, Serbian and Spanish by [Ogi Djuraskovic](http://firstsiteguide.com)


= Requirements =

PHP 5.2+


== Installation ==

1. Install ZIP file or upload plugin files to the `/wp-content/plugins/pwebonedrive` directory.
1. **Activate the plugin** through the Plugins menu in WordPress.
1. Create **Windows Live ID** account for [OneDrive](https://onedrive.live.com/) if you do not have already
1. **Register your site** in [Windows Live application management](https://account.live.com/developers/applications/index) that WordPress could connect to your OneDrive.
Remember to set your site URL as **Redirect domain** and **Mobile client app: No**, **Enhanced redirection security: Disabled**.
[Read how to get your Client ID](http://msdn.microsoft.com/library/cc287659.aspx).
1. Go to **plugin configuration** panel and set all details from **Windows Live application management**.
1. Disable pop-up windows blocking, because OneDrive would display login and permissions screen in a new pop-up window.
1. Edit post and use one of new blue buttons in editor.


== Frequently Asked Questions ==

[Visit full documentation page](http://www.perfect-web.co/wordpress/microsoft-onedrive-gallery-file/documentation)


**Warning! OneDrvie plugin is not working with Windwos Live Applications created after 1st of June 2014. We are waiting for update of Microsoft OneDrive API documentation to make necessary changes in our software.**


= First use of OneDrive buttons =

Disable pop-up windows blocking, because OneDrive would display login and permissions screen in a new pop-up window.

= Blank Lightbox or error =

If you clicked OneDrive button in editor and nothing has happened after a few minutes or a blank Lightbox window has display with message: Error Microsoft OneDrive, then remove cookie named: wl_auth and reload post edit page.

Use OneDrive emergency exit button to close all blank layers.

Remember to save post before using OneDrive buttons or at least before reloading page.

= Error in Microsoft popup =

If you got message: *Microsoft account is experiencing technical problems* in popup window then check **error_description** of URL in address bar.

If you have following error description:
*The provided value for the input parameter 'redirect_uri' is not valid. The expected value is 'https://login.live.com/oauth20_desktop.srf' or a URL which matches the redirect URI registered for this client application*
then go to Windows Live application management and set in your application **Enhanced redirection security: Disabled**.

If you have other error description then send it to our support.

= Debugging =

Enable debug in WordPress [read how to](https://codex.wordpress.org/Debugging_in_WordPress).

Go to Post editor. 
Remove **wl_auth cookie**. 
Reload page. 
Try to use OneDrive button again. 
You will see some alerts with debug informations.

Check also `/wp-content/debug.log` file. Read only lines with *pweb_onedrive* or *LiveConnectClient*.
If you would not find there any error message then send this log file to our support.


== Screenshots ==

1. Buttons in post editor
2. Sign in to OneDrive
3. Grant permissions
4. Select a folder for gallery
5. Select a file for a download link
6. Shortcodes in editor
7. Configuration

== Changelog ==

= 1.0.5 / 11-06-2014 =
* Fixed hook where wp_register_style is used

= 1.0.4 / 22-04-2014 =
* Added Serbian and Spanish translations by Ogi Djuraskovic

= 1.0.3 / 07-04-2014 =
* Fixed cookie domain name
* Fixed request timeout and SSL verify

= 1.0.2 / 02-04-2014 =
* Inform about new option on Live Connect app management

= 1.0.1 / 31-03-2014 =
* Backward compatibility with PHP 5.2

= 1.0.0 / 28-02-2014 =
* First release

== Upgrade Notice ==

N/A