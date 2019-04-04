# P3 Neutraliser

This plugin prevents Pipdig's P3 plugin from updating or calling home to malicious Pipdig URLs that could harm your site. See [Jem Turner's post][jempost] for information on the background.

[jempost]: https://www.jemjabella.co.uk/2019/pipdig-your-questions-answered/

This could be useful if you want to find a new theme, but don't have the time or money or skills to do it right now.

With this plugin active, P3 should not be able to call out to malicious URLs or update itself with new malicious code.

## Installation

1. [Download the latest release][release] as a zip file.
2. Navigate to `Plugins > Add New` on your Site.
3. Click "Upload Plugin" up at the top.
4. Upload the zip file you downloaded.
5. Activate the P3 Neutraliser plugin.

[release]: https://github.com/markjaquith/p3-neutraliser/releases/download/v1.0.0/p3-neutraliser-v1.0.0.zip

## Notes

Note that this plugin doesn't prevent injection of Pipdig JavaScript into your site, as doing so would likely result in many functions of your site breaking.

It also doesn't block calls to `pipdig.rocks`, as these calls don't trigger anything bad and they are necessary for some of P3's social features like updating your Pinterest and Instagram counts, etc.

## FAQ

### I'm on Blogger, can I use this?

No. This is for people using Pipdig's WordPress themes.

### So I can keep using my Pipdig WordPress theme?

You can, but as long as this plugin is active, neither it nor the P3 plugin will receive any updates. And many of your Pipdig theme's features depend on Pipdig to keep certain JavaScript files hosted on their sites. They could take these files down or change them at any time (and as of this writing, their web host has already taken some of them down). This could affect how your site looks or functions. I can't promise that your site will be 100% fine forever.
