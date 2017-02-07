# DokuWiki-Glip

A DokuWiki plugin that notifies a [Glip](http://glip.com) room of wiki edits.

## Setup

1. Clone repository into your DokuWiku plugins folder, making the target folder name 'glip'
```
$ git clone https://github.com/twinklebob/dokuwiki-glip.git glip
```

2. In your DokuWiki Configuration Settings, enter a webhook URL, the URL to an icon (DokuWiki don't encourage using their hosted image) and the title you want to give the notifications in Glip.

3. Optionally, you can also define a comma-separated list of first-level namespaces to limit notifications to only those namespaces (without this setting, all namespaces will trigger notifications)

## Requirements

* DokuWiki
* For communication with Glip's webhooks you'll need SSL installed

## Thanks

This code was adapted from the Kato DokuWiki plugin https://github.com/kato-im/dokuwiki-kato
