=== KCSG Kartra Pages ===
Contributors: bkatzung
Donate link: https://kcsg.krtra.com/t/U8MKk5qeQXYf
Tags: Kartra, KCSG, Tools For Kartra, pages, loading, embedding, templates
Requires at least: 5.2.4
Tested up to: 5.3
Stable tag: 0.1.3
Requires PHP: 5.6.30
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display Kartra pages in WordPress ("live" or downloaded) *BETA*

== Description ==

KCSG Kartra Pages (KKP) extends any theme by adding a custom page template specifically designed for displaying Kartra-built pages via your WordPress site.

**Note: This plugin is currently in beta testing.**

If you would like to particpate, please notify [Brian Katzung](https://m.me/briankatzung) and join the [KCSG Tools For Kartra Facebook group](https://facebook.com/groups/kcsgtfk).

The template operates in one of three modes chosen from controls provided in the page editor:

= "WordPress" Mode =

In WordPress mode (the default), KKP just displays the native WordPress content on a minimal page. This mode is equivalent to the Blank Slate plugin by Aaron Reimann et al.

This mode is only intended to be a "reasonable fallback" when the template has insufficient information to deliver anything else.

While not recommended, it is possible to display a Kartra page in this mode by copying-and-pasting Kartra's "Embed code" into a WordPress custom HTML block.

Once set up, a WordPress visitor's browser would display the Kartra page by following this four-step process:

1. The browser loads the Kartra embed code from the WordPress custom HTML block
2. The browser loads a page-loader script from Kartra based on the embed code
3. The browser loads the Kartra page from Kartra based on the page-loader script
4. The browser loads any additional assets referenced by the Kartra page

= "Kartra Live" Mode =

Kartra Live mode produces a "live Kartra page in an iframe" result similar
to WordPress mode, but with a couple of differences:

1. You paste the Kartra Embed code (or URL portion from it) into the Embed code/URL input of the KCSG Kartra Pages controls area in the page editor instead of into a custom HTML block.
2. When you select the Kartra Live option and click Apply, KKP saves a copy of SEO settings and Open Graph data from Kartra in WordPress.
3. The custom page loader includes the SEO and Open Graph data previously fetched from Kartra and serves the second-stage page loader script directly from WordPress.
4. The custom page loader uses your WordPress site icons instead of the Kartra site icons (so you won't need to associate your Kartra pages with a custom domain in order to get the site icon branding that's already on your WordPress domain).

Once set up, A WordPress visitor's browser will display a Kartra page in Kartra Live mode by following this three-step process:

1. The browser loads a custom page-loader script from WordPress
2. The browser loads the Kartra page from Kartra based on the page-loader script
3. The browser loads any additional assets referenced by the Kartra page

In this mode, you do not need to do anything in WordPress when you publish page changes in Kartra unless you update SEO and/or Open Graph settings (in which case you'll need to click "Apply" to update the settings stored in WordPress).

Important: Because this mode is tuned for displaying Kartra-built pages, it elminates most of the overhead for supporting WordPress pages. A consequence of this is that any WordPress configuration for applying things like pixels, tracking, analytics, etc. will likely not work in Kartra Live mode. Use the corresponding Kartra page settings instead.

= "Kartra Download" Mode =

In Kartra Download mode, a snapshot of the most recently published Kartra page (but with your WordPress site icons instead of Kartra site icons) is stored in, and served directly from, the WordPress database.

To configure:

1. Paste the Kartra Embed code (or URL portion from it) into the Embed code/URL input of the KCSG Kartra Pages controls area in the page editor.
2. When you select the Kartra Download option and click Apply, KKP downloads and stores the most recently published version of the Kartra Page in the WordPress database. (Whenever you publish changes on Kartra, just click Apply again in the WordPress page editor to update the version saved in WordPress.)

Once set up, a WordPress visitor's browser will display a Kartra page in Kartra Download mode by following this two-step process:

1. The browser loads the downloaded-and-saved Kartra page directly from WordPress
2. The browser loads any additional assets referenced by the Kartra page 

Important: Per Kartra, due to cross-domain cookie restrictions, some tracking might not be accurate in download-to-server configurations (including KKP's Kartra Download mode). On pages where this is a concern, please use Kartra Live mode instead.

== Installation ==

1. Upload the plugin files to the "/wp-content/plugins/kcsg-kartra-pages" directory, or install the plugin via the WordPress plugins screen.
1. Activate the plugin via the WordPress plugins screen.

See the "Operation" section for operating instructions.

== Operation ==

1. Create a new page or edit an existing page where you want your Kartra page to appear (the template and controls are only available for pages, not for posts).
1. Select the "KCSG Kartra Page" template from the Template drop-down in the Page Attributes section and save or update the page. Do NOT use the KCSG Kartra Pages "Apply" button for this step.
1. Locate the "KCSG Kartra Pages" controls section below the page content and click the open arrow to open it if necessary.
1. Choose the desired template mode.
1. In order to use the Kartra modes, you must supply a source URL. You can paste any portion of Kartra's page loader Embed code for the corresponding Kartra page as long as it includes the full source URL. KKP will extract the URL and discard the rest. This value will be remembered, so you don't need to supply it again unless you want to change it.
1. Click the "Apply" button in the KCSG Kartra Pages control section. In Kartra Download mode, the stored content will be updated from the latest published version of your Kartra page. If successful, you will see a "Request complete" message.

== Frequently Asked Questions ==

= What are the advantages of Kartra Live mode over Kartra Download mode? =

In Live mode, the content is always loaded directly from Kartra's infrastructure, so visitors will always get the most recently published version without any additional action required on your part.

Content is delivered in iframes using the standard kartra domains, so tracking and analytics should all work as normal.

= What are the advantages of Kartra Download mode over Kartra Live mode? =

Since your content is being served directly from your WordPress domain, it will definitely be attributed to your WordPress domain by search engines.  (In Live mode, search engines might attribute your content to your Kartra domain.)

Since Download-mode content is regular HTML and doesn't involve several levels of JavaScript indirection, search engines that don't understand JavaScript will still be able to index your content.

Download mode does more work in advance so that fewer steps and fewer network requests and responses are required to display the final page.

Important: Per Kartra, due to cross-domain cookie restrictions, some tracking might not be accurate in download-to-server configurations (including KKP's Kartra Download mode). On pages where this is a concern, please use Kartra Live mode instead.

= How does KCSG Kartra Pages compare to Aaron Reimann's Blank Slate? =

KKP's WordPress mode should be virtually identical in function to Blank Slate.

In comparison, KKP's Kartra Live mode...

* Uses a very-low-overhead, Kartra-tuned page-loading process
* Is designed to reduce the chances of page-loading loops sometimes observed when using Blank Slate
* Saves a custom page loader in WordPress, saving a step in the display process
* Provides SEO and Open Graph information even without JavaScript execution (improving SEO and allowing things like Facebook link details to display properly)
* Uses your WordPress site icons instead of Kartra site icons

KKP's Kartra Download mode:

* Saves a snapshot of your published Katra page (also including SEO and Open Graph information) directly in the WordPress database, eliminating several steps in the display process
* Also uses your WordPress site icons instead of Kartra site icons
* Ensures SEO efforts will be attributed to your WordPress domain

= Where can I get support or more information? =

For support, please use the help desk on the [Tools For Kartra page](https://kcsg.kartra.com/page/tools-for-kartra).  For general information and announcements, please join the [KCSG Tools For Kartra Facebook group](https://facebook.com/groups/kcsgtfk).

Be advised that this is a free plugin provided on an "as-is" basis, not a commercially supported product. The author(s) will do their best to provide support in their available time.

== Privacy Policy ==

This plugin collects and uses the Kartra page URLs you enter, along with associated alternative URLs and published Kartra page content, for the sole purpose of displaying the specified Kartra pages to your WordPress website visitors.

No other information is collected, used, or disclosed.

== Disclaimer ==

KCSG Kartra Pages is a free plugin by Brian Katzung of Kappa Computer Solutions, LLC and BusRes.com Business Resources. It is not affiliated with, endorsed, or supported by Kartra or Genesis Digital.

== Screenshots ==

1. This screenshot shows the selection of the KCSG Kartra Page page template and the KCSG Kartra Pages control section.

== Videos ==

Here's a demonstration of installation and operation:

https://youtu.be/Aj0EitFeisM

== Upgrade Notice ==

= 0.1.3 =

Pages might not display properly in WordPress mode without this upgrade!

= 0.1.1 =

Improve compatibility with classic editor.

= 0.1.0 =

SEO and Open Graph is now stored in WordPress for Kartra Live mode (and updated when you click "Apply"). This allows Facebook link details to be displayed, for example. This update is therefore highly recommended if you use Live mode.

= 0.0.11 =

This includes an important update if you (might ever) use the classic editor.

== Changelog ==

= 0.1.3 =

* Fixed WordPress-mode closing </title> tag.

= 0.1.2 =

* Fixed default template mode selection.

= 0.1.1 =

* Improve compatibility with classic editor.

= 0.1.0 =

* Kartra Live mode now caches SEO and Open Graph information in WordPress

= 0.0.11 =

* Changed the template settings implementation to be backward-compatible with the classic editor.

= 0.0.10 =

* Added demonstration video

= 0.0.9 =

* Documentation update regarding WordPress pixels/tracking/analytics in Kartra Live mode.

= 0.0.8 =

* Correction to Operation documentation and general formatting.

= 0.0.7 =

* Revised page content escaping per *_post_meta documentation.

= 0.0.6 =

* Update 1 for WordPress coding standards/review

= 0.0.5 =

* Remove mode from database when "blank" and URL from database when blank.

= 0.0.4 =

* Store just the page URL and not the page loader in script/live mode
* Improve URL validation
* Accept kartra.com page links as well as embed codes or URLs

= 0.0.3 =

* Added internationalization support
* Changed blank, script, and cache modes in the user interface to be WordPress, Kartra Live, and Kartra Download to be more clear and less technical
* Changed script/live mode to cache a custom, second-stage page loader instead of using a mostly-stock, first-stage embed script
* Removed unused post support (since it only works for pages)

= 0.0.2 =

* First release to beta testers
