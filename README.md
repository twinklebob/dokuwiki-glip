# DokuWiki-Glip

A DokuWiki plugin that notifies a [Glip](http://glip.com) room of wiki edits.

## Setup

1. Clone repository into your DokuWiku plugins folder, making the target folder name 'glip'
```
$ git clone https://github.com/twinklebob/dokuwiki-glip.git glip
```

2. In your DokuWiki Configuration Settings, enter a URL and the name you want the notifications to appear from in Glip.

3. Optionally, you can also define a comma-separated list of first-level namespaces to limit notifications to only those namespaces (without this setting, all namespaces will trigger notifications)

## Requirements

* DokuWiki
* [mandatory] PHP's cURL module

## Thanks

This code was adapted from the Kato DokuWiki plugin https://github.com/kato-im/dokuwiki-kato
