=== KCSG Kartra Pages ===
Contributors: bkatzung
Donate link: https://kcsg.krtra.com/t/U8MKk5qeQXYf
Tags: Kartra, KCSG, Tools For Kartra, pages, loading, embedding, templates
Requires at least: 5.2.4
Tested up to: 5.2.4
Stable tag: 0.0.3
Requires PHP: 5.6.30
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display Kartra-built pages via WordPress. Supports embed (iframe) *AND*
download-to-server modes.

== Description ==

KCSG Kartra Pages (KKP) extends any theme by adding a custom page template
specifically designed for displaying Kartra-built pages via your WordPress
site.

The template operates in one of three modes chosen from controls provided
in the page editor.

The template defaults to "Blank WordPress" mode, which just displays the
native WordPress content on a blank page. This mode is equivalent to the
Blank Slate plugin by Aaron Reimann et al.

In this mode, you could use it just like Blank Slate by pasting the page
loader embed script supplied by Kartra into a custom HTML section.

Alternatively, you can just paste the page loader embed script (or its source
URL) into the source setting for the template. This skips the need for the
custom HTML section and makes it possible to use the two additional template
modes.

"Script" mode in conjunction with the source setting works like Blank Slate
with a custom HTML block, except that the page loader embed script is
injected into an "ultra-lean" page specifically tuned for Kartra with less
overhead than Blank Slate.

Kartra's page loader embed script will load the Kartra page content from
Kartra's CloudFlare servers via an iframe and set the page title and other
attributes.

In "cache" mode, the current Kartra page content will be downloaded and
stored in WordPress each time you click on the Apply button. This page
content will then be served directly from your WordPress site without the
need for an iframe.

== Installation ==

1. Upload the plugin files to the "/wp-content/plugins/kcsg-kartra-pages" directory, or install the plugin via the WordPress plugins screen.
1. Activate the plugin via the WordPress plugins screen.

== Operation ==

1. Create a new page or edit an existing page where you want your Kartra page to appear (the template and controls are only available for pages, not for posts).
1. Select the "KCSG Kartra Page" template from the Template drop-down in the Page Attributes section.
1. Locate the "KCSG Kartra Pages" controls section below the page content and click the open arrow to open it if necessary.
1. Choose the desired template mode.
1. In order to use the "script" or "cache" modes, you must supply a source URL. You can paste any portion of the page loader embed code provided by Kartra as long as it includes the full source URL. KKP will extract the URL and discard the rest. This value will be remembered, so you don't need to supply it again unless you want to change it.
1. Click Apply. In "cache" mode, the stored content will be updated from the latest published version of your Kartra page. If successful, you will see a "Request complete" message.

== Frequently Asked Questions ==

= What are the advantages of script mode over cache mode? =

In script mode, the content is always loaded directly from Kartra's
infrastructure, so visitors will always get the most recently published
version without any additional effort on your part.

Since content comes from Kartra domains, tracking and analytics should
work exactly the same.

Page-loading performance might be better (or the same, or worse), depending
on numerous static and dynamic factors.

= What are the advantages of cache mode over script mode? =

Since your content is being served directly from your WordPress domain, it
will definitely be attributed to your WordPress domain by search engines.
With the page loader embed script, it's being served by an iframe with your
Kartra domain, and therefore might be attributed to your Kartra domain
instead.

Since cache-mode content is regular HTML and doesn't involve a
JavaScript-generated iFrame, search engines that don't understand
JavaScript will still be able to index your content.

Page-loading performance might be better (or the same, or worse), depending
on numerous static and dynamic factors.

= How does KCSG Kartra Pages compare to Aaron Reimann's Blank Slate? =

KKP's "Blank WordPress" mode should be virtually identical in function to
Blank Slate.

KKP's "script" mode has lower overhead than Blank Slate because it is
specifically tuned for the way Kartra's page loader embed script works.

KKP's "cache" mode incorporates a Kartra-specific process for downloading
Kartra-built pages and displaying them directly from WordPress. Blank Slate
does not, because it's a more general-purpose plugin.

== Changelog ==

= 0.0.3 =
* Added internationalization support
* Removed unused post support (since it only works for pages)

= 0.0.2 =
* First release to beta testers
