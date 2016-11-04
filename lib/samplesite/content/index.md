---
Site: Pico for Nextcloud
Title: Pico for Nextcloud
Theme: default
Description: Pico is a stupidly simple, blazing fast, flat file CMS.
social:
    - title: Visit us on GitHub
      url: https://github.com/picocms/Pico
      icon: octocat
    - title: Check us out on Twitter
      url: https://twitter.com/gitpicocms
      icon: birdy
    - title: Join us on Freenode IRC Webchat
      url: https://webchat.freenode.net/?channels=%23picocms
      icon: chat
---

## Welcome

Congratulations, you are now using [Pico](http://picocms.org/).
%meta.description% <!-- replaced by the above Description meta header -->

## Creating Content

Pico is a flat file CMS. This means there is no administration backend or
database to deal with. You simply create `.md` files in a `content` folder
and those files become your pages. For example, this file is called `index.md`
and is shown as the main landing page.

Pico for Nextcloud is an app for Nextcloud, allowing to designate any folder
in your Nextcloud account as site folder. This is done in your preferences.
Once a site folder is designated, you can populate the `content` subfolder
with `.md` files and also add your own themes in the `themes` subfolder.
You can start by making a copy of the present web site by simply copying the
`content` folder from the folder `samplesite` shared with you. Then you can
change it as you like.

You can structure your site with subfolders. E.g. for the present site,
`%site_title%`, there's a folder within the content folder,
`%site_title%/content/sub`, containing an `index.md`. You can access that
folder at the URL `%base_url%/sub`. If you want another page within the sub folder,
simply create a text file with the corresponding name and you will be able to
access it (e.g. `content/sub/page.md` is accessible from the URL
`%base_url%/sub/page`). Below is shown some examples of locations
and their corresponding URLs:

<table style="width: 100%; max-width: 40em;">
    <thead>
        <tr>
            <th style="width: 50%;">Physical Location</th>
            <th style="width: 50%;">URL</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>content/index.md</td>
            <td><a href="%base_url%">/</a></td>
        </tr>
        <tr>
            <td>content/sub.md</td>
            <td><del>sub</del> (not accessible, see below)</td>
        </tr>
        <tr>
            <td>content/sub/index.md</td>
            <td><a href="%base_url%/sub">/sub</a> (same as above)</td>
        </tr>
        <tr>
            <td>content/sub/page.md</td>
            <td><a href="%base_url%/sub/page">/sub/page</a></td>
        </tr>
        <tr>
            <td>content/a/very/long/url.md</td>
            <td>
              <a href="%base_url%/a/very/long/url">/a/very/long/url</a>
              (doesn't exist)
            </td>
        </tr>
    </tbody>
</table>

If a file cannot be found, the file `content/404.md` will be shown. You can add
`404.md` files to any directory. So, for example, if you wanted to use a special
error page for your blog, you could simply create `content/blog/404.md`.

### Text File Markup

Text files are marked up using [Markdown][] and [Markdown Extra][MarkdownExtra].
They can also contain regular HTML.

In the case of Pico for Nextcloud, we've included LaTeX support via [MathJax](www.mathjax.org),
allowing you to directly use expressions like:

```
When $a \ne 0$, there are two solutions to $\(ax^2 + bx + c = 0\)$ and they are
$$x = {-b \pm \sqrt{b^2-4ac} \over 2a}.$$
```

Resulting in:

When $a \ne 0$, there are two solutions to $\(ax^2 + bx + c = 0\)$ and they are
$$x = {-b \pm \sqrt{b^2-4ac} \over 2a}.$$

At the top of text files you can place a block comment and specify certain meta
attributes of the page using [YAML][] (the "YAML header"). For example:

    ---
    Title: Welcome
    Description: This description will go in the meta description tag
    Author: Joe Bloggs
    Date: 2013/01/01
    Robots: noindex,nofollow
    Template: index
    Theme: my_cool_theme
    
    ---

These values will be contained in the `{{ meta }}` variable in themes
(see below).

There are also certain variables that you can use in your text files:

* <code>&#37;site_title&#37;</code> - The title of your Pico site
* <code>&#37;base_url&#37;</code> - The URL to your Pico site; internal links
  can be specified using <code>&#37;base_url&#37;?sub/page</code>
* <code>&#37;theme_url&#37;</code> - The URL to the currently used theme
* <code>&#37;meta.&#42;&#37;</code> - Access any meta variable of the current
  page, e.g. <code>&#37;meta.author&#37;</code> is replaced with `Joe Bloggs`

Pico for Nextcloud adds a few more:

* <code>&#37;user&#37;</code> - On a page marked with meta attribute <code>Access: private</code>,
the user name of a logged-in visitor with access rights to the page in question
* <code>&#37;group&#37;</code> - On a page marked with meta attribute <code>Access: private</code>,
the group of a logged-in visitor that gives him access to the page in question
* <code>&#37;owner&#37;</code> - The owner of the page in question
* <code>&#37;master_url&#37;</code> - In a distributed setup, the URL of the master server that
redirects to the slave servers holding the data. A site may e.g. have URL
<code>%master_url%sites/mysite</code>, but be physically hosted at
<code>https://someslave.somesite/sites/mysite</code>, and the actual file located at
<code>https://someslave.somesite/shared/mysite/content/index.md</code>

## Customization

Pico is highly customizable in two different ways: On the one hand you can
change Pico's appearance by using themes, on the other hand you can add new
functionality by using plugins. Doing the former includes changing Pico's HTML,
CSS and JavaScript, the latter mostly consists of PHP programming.

In the case of Pico for Nextcloud, for security reasons, users cannot add
plugins. An administrator can simply add plugins in the `plugins` folder.

Moreover, special meta attributes can be used to customize individual pages:

* <code>Theme</code> - The theme used to style the page
* <code>Access</code> - Access rights to the page - either `public` or
  `private`. The default is `public`. When set to `private`, access is granted
  using the access rights to the file, i.e. who it has been shared with.

This is all Greek to you? Don't worry, you don't have to spend time on these
techie talk - it's very easy to use one of the great themes or plugins others
developed and released to the public. Please refer to the next sections for
details.

### Themes

You can create themes in your `themes` folder. Check
out the `default` theme for an example. Pico uses [Twig][] for template
rendering. You can select your theme by setting the meta attribute `Theme`
to the name of your theme folder.

All themes must include an `index.twig` (or `index.html`) file to define the
HTML structure of the theme. Below are the Twig variables that are available
to use in your theme. Please note that paths (e.g. `{{ base_dir }}`) and URLs
(e.g. `{{ base_url }}`) don't have a trailing slash.

* `{{ base_url }}` - The URL to your Pico site; use Twigs `link` filter to
                     specify internal links (e.g. `{{ "sub/page"|link }}`),
                     this guarantees that your link works whether URL rewriting
                     is enabled or not
* `{{ theme_url }}` - The URL to the currently active theme
* `{{ rewrite_url }}` - A boolean flag indicating enabled/disabled URL rewriting
* `{{ site_title }}` - Shortcut to the site title
* `{{ meta }}` - Contains the meta values from the current page
    * `{{ meta.title }}`
    * `{{ meta.description }}`
    * `{{ meta.author }}`
    * `{{ meta.date }}`
    * `{{ meta.date_formatted }}`
    * `{{ meta.time }}`
    * `{{ meta.robots }}`
    * ...
* `{{ content }}` - The content of the current page
                    (after it has been processed through Markdown)
* `{{ pages }}` - A collection of all the content pages in your site
    * `{{ page.id }}` - The relative path to the content file (unique ID)
    * `{{ page.url }}` - The URL to the page
    * `{{ page.title }}` - The title of the page (YAML header)
    * `{{ page.description }}` - The description of the page (YAML header)
    * `{{ page.author }}` - The author of the page (YAML header)
    * `{{ page.time }}` - The timestamp derived from the `Date` header
    * `{{ page.date }}` - The date of the page (YAML header)
    * `{{ page.date_formatted }}` - The formatted date of the page
    * `{{ page.raw_content }}` - The raw, not yet parsed contents of the page;
                                 use Twigs `content` filter to get the parsed
                                 contents of a page by passing its unique ID
                                 (e.g. `{{ "sub/page"|content }}`)
    * `{{ page.meta }}`- The meta values of the page
* `{{ prev_page }}` - The data of the previous page (relative to `current_page`)
* `{{ current_page }}` - The data of the current page
* `{{ next_page }}` - The data of the next page (relative to `current_page`)
* `{{ is_front_page }}` - A boolean flag for the front page

Pages can be used like the following:

    <ul class="nav">
        {% for page in pages %}
            <li><a href="{{ page.url }}">{{ page.title }}</a></li>
        {% endfor %}
    </ul>

Additional to Twigs extensive list of filters, functions and tags, Pico also
provides some useful additional filters to make theming easier. You can parse
any Markdown string to HTML using the `markdown` filter. Arrays can be sorted
by one of its keys or a arbitrary deep sub-key using the `sort_by` filter
(e.g. `{% for page in pages|sort_by("meta:nav"|split(":")) %}...{% endfor %}`
iterates through all pages, ordered by the `nav` meta header; please note the
`"meta:nav"|split(":")` part of the example, which passes `['meta', 'nav']` to
the filter describing a key path). You can return all values of a given key or
key path of an array using the `map` filter (e.g. `{{ pages|map("title") }}`
returns all page titles).

You can use different templates for different content files by specifying the
`Template` meta header. Simply add e.g. `Template: blog-post` to a content file
and Pico will use the `blog-post.twig` file in your theme folder to render
the page.

You don't have to create your own theme if Pico's default theme isn't
sufficient for you, you can use one of the great themes third-party developers
and designers created in the past. As with plugins, you can find themes in
[our Wiki][WikiThemes].

## Documentation

For more help have a look at the Pico documentation at http://picocms.org/docs.

[Markdown]: http://daringfireball.net/projects/markdown/syntax
[MarkdownExtra]: https://michelf.ca/projects/php-markdown/extra/
[YAML]: https://en.wikipedia.org/wiki/YAML
[Twig]: http://twig.sensiolabs.org/documentation
[WikiThemes]: https://github.com/picocms/Pico/wiki/Pico-Themes
[WikiPlugins]: https://github.com/picocms/Pico/wiki/Pico-Plugins
[PluginUpgrade]: http://picocms.org/development/#upgrade
[ModRewrite]: https://httpd.apache.org/docs/current/mod/mod_rewrite.html
[NginxConfig]: http://picocms.org/in-depth/nginx/
