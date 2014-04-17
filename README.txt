=== Forwarded Host URLs ===
Contributors: blahed
Tags: plugin, urls, hostname, host, links
Stable tag: 0.0.6
License: MIT

Forces WordPress to build urls using the X-Forwarded-Host header, if it exists.

== Description ==

A plugin to force WordPress to build urls based on the X-Forwarded-Host header. Useful if you're viewing a WordPress site using a proxy and the proxy sets the X-Forwarded-Host header (like https://forwardhq.com).

We don't recommend using this in production, but it can be useful for development.

Ideas from https://gist.github.com/949821 and http://odyniec.net/blog/2010/02/wordpress-blog-and-multiple-server-names/

