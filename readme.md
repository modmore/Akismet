Akismet: Spam Protection for MODX
=

Developed by [modmore](https://modmore.com)

Introduction
-

[Akismet](https://akismet.com/) is an advanced spam protection service that uses AI to analyse form submissions. It learns from spam patterns around the web in real-time, and is extremely effective at blocking spam without hindering the user experience with CAPTCHAs.

Originally developed for Wordpress, this open source package integrates Akismet with the MODX extras
[FormIt](https://docs.modx.com/current/en/extras/formit), [Login](https://docs.modx.com/current/en/extras/login) (specifically the [Register](https://docs.modx.com/current/en/extras/login/login.register) snippet), and [Quip](https://docs.modx.com/current/en/extras/quip).

The provided MODX snippet ***Akismet*** is used as a *[hook](https://docs.modx.com/3.x/en/extras/formit/formit.hooks)* with FormIt, and a *[preHook](https://docs.modx.com/3.x/en/extras/login/login.tutorials/using-pre-and-post-hooks)* with Register and Quip. Note that hooks for Quip are not documented, but you can add `&preHooks` to the [QuipReply snippet](https://docs.modx.com/current/en/extras/quip/quip.quipreply).  

Akismet is free for personal sites or blogs, and requires a paid subscription for use on commercial websites. [Learn more about Akismet's subscription model](https://akismet.com/plans/).  

Installation
-
Install Akismet via the [modmore package provider](https://modmore.com/about/package-provider/). Sign up for an Akismet account [here](https://akismet.com/plans/), then copy and paste the provided *API Key* into the new `akismet.api_key` system setting.  


Usage with FormIt
-
Within your FormIt snippet call, add `Akismet` as one of your hooks. Preferably the first one, as to prevent other hooks running if spam is detected.

```
[[!FormIt? 
    &hooks=`Akismet,email,redirect`
    ...
]]
```

Usage with Login
-
Within your Register snippet call, add `Akismet` as one of your *preHooks*.

```
[[!Register?
    &preHooks=`Akismet`
    ...
]]
```

Usage with Quip
-
Within your QuipReply snippet call, add `Akismet` as one of your *preHooks*.

```
[[!QuipReply?
    &preHooks=`Akismet`
    ...
]]
```

Configurable Fields
-
Since Akismet was originally developed for Wordpress, it accepts fields that are related to comments on blog posts, 
such as `comment_author`, `comment_author_email` and `comment_content`.

MODX allows any naming convention for fields, so you **set the field names you're using as snippet parameters**. This works with FormIt, Register and Quip. 

Say for example, you have a contact form with the following fields: `name`, `email` and `message`. 
You can set these to the fields that the Akismet service is expecting. See this code example:

```
[[!FormIt? 
    &hooks=`Akismet,email,redirect`
    &akismetAuthor=`name`
    &akismetAuthorEmail=`email`
    &akismetContent=`message`
    
    &akismetTest=`1`
    &akismetType=`contact-form`
    &akismetHoneypotField=`nospam`
    ...
]]
```

**Complete List of Parameters**

- `&akismetAuthor` - The author's name.
- `&akismetAuthorEmail` - The author's email.
- `&akismetAuthorUrl` - The author's URL if they provided one.
- `&akismetContent` - The message content.
- `&akismetType` - The type of form submitted. Available types include *comment*, *forum-post*, *reply*, *blog-post*, *contact-form*, *signup*, *message*, and more. Read more [here](https://blog.akismet.com/2012/06/19/pro-tip-tell-us-your-comment_type/).
- `&akismetUserRole` - The type of user e.g. *visitor*, or *member*. If set to *Administrator*, the form will never be blocked.
- `&akismetTest` - Set this to `1` while developing so the AI knows it is just a test submission.
- `&akismetHoneypotField` - If you use a hidden honeypot field in your form, set the name of it here.
- `&akismetRecheckReason` - If you have a form where the same submission needs to be checked more than once, include the reason for it here.
- `&akismetError` - The error message to set when the form failed the spam check. By default, this will use the `akismet.message_blocked` lexicon, which you may edit via System > Lexicon Management > akismet (select in the namespace dropdown), or you can provide the snippet property with a different message entirely. 

Combining Fields
-

Perhaps your web form has separate fields for a persons first name and last name. Many do! Akismet expects a single author 
field however, so from v1.1 onwards, you can combine fields by adding the field names together separated by commas.

For example:

```
&akismetAuthor=`first_name,last_name`
&akismetContent=`main_content_field,another_content_field`
```

Automatic Cleanup
-

By default, Akismet will remove spam checks that are more than 30 days old. This period can be adjusted with the `akismet.cleanup_days_old` system setting. 

To disable automatic cleanup, set `akismet.cleanup_days_old` to `0`. 

The cleanup does not require a cron job. It stores a timestamp in `core/components/akismet/.cleanup` and reads that every time a spam check is performed. If it's been more than the configured days since a cleanup happened, it will remove old checks right at that time.
