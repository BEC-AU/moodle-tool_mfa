<a href="https://travis-ci.org/catalyst/moodle-tool_mfa">
<img src="https://travis-ci.org/catalyst/moodle-tool_mfa.svg?branch=master">
</a>

# moodle-tool_mfa

* [What is this?](#what-is-this)
* [Why another MFA plugin for Moodle?](#why-another-mfa-plugin-for-moodle)
* [Branches](#branches)
* [Installation](#installation)
* [Configuration](#configuration)
  * [Factors](#factors)
  * [Scores](#scores)
  * [Examples](#examples)

* [Support](#support)

## What is this?

This is a Moodle plugin which adds Multi-Factor authentication (MFA), also known as Two-factor authentication (2FA) on top of your existing chosen authentication plugins.

https://en.wikipedia.org/wiki/Multi-factor_authentication

## Why another MFA plugin for Moodle?

There are other 2FA plugins for moodle such as:

https://moodle.org/plugins/auth_a2fa

This one is different because it is NOT a Moodle authentication plugin. It leverages new API's that Catalyst specifically implemented in Moodle Core to enable plugins to *augment* the login process instead of replacing it. This means that this MFA plugin can be added on top of any other authentication plugin resulting in a much cleaner architecture, and it means you can compose a solution that does everything you need instead of compromising by swapping out the entire login flow.

See this tracker and the new dev docs for more info:

https://tracker.moodle.org/browse/MDL-66173

https://docs.moodle.org/dev/Login_callbacks

That other difference is that we intend to support multiple authentication factor *types* in this plugin, eg TOPT or SMS or whatever else as sub-plugins. Initially we will only supoprt TOPT but the idea is that you can configure it to allow multiple types, or let the use decide which or various options they would prefer, and most of the authenticatin flow augmentation in shared regardless of which type is in use.

## Branches

`master` is considered stable and supports these versions, with the caveat of backporting the API's needed.

## Installation

## Configuration

The main concept to understand is the concept of factors. You must have some combination of factors which each contribute to you overall score. 

### General settings

* Grace period


### Factors

#### IP Range

This is so you can say that if you are on a secure network then that counts for something. This is very useful because you can set it up so that you can login via a secure network, to then configure say TOTP, and then use that for logging in when not on a secure network.

#### TOTP

This is standard TOTP using Google Authenticator or any other app which conforms to the open standard.

#### Auth Type

This is so you can specify that logging in via say SAML via ADFS which may have already done it's own MFA checks has a certain score which might satisfy the criteria completely and effectly make it exempt from additional checks.

#### Email

A simple factor which sends a short lived code to your email which you then need to enter to login. Generally speaking this is a low security factor because typically the same username and password which logs you into moodle is the same which logs you into your email so it doesn't add much value.


### Scores and examples

If your score is high enough then you are able to login. Scores can be weighted for different factors.


## Support

If you have issues please log them in github here

https://github.com/catalyst/moodle-auth_saml2/issues

Please note our time is limited, so if you need urgent support or want to
sponsor a new feature then please contact Catalyst IT Australia:

https://www.catalyst-au.net/contact-us

This plugin was developed by Catalyst IT Australia:

https://www.catalyst-au.net/

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400">
