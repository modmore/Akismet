Akismet: Spam Protection for MODX
=

Developed by [modmore](https://modmore.com)

Introduction
-

[Akismet](https://akismet.com/) is an advanced spam protection service that uses AI to analyse form submissions. 
Originally developed for Wordpress, this open source package integrates Akismet with the MODX extras
[FormIt](https://docs.modx.com/current/en/extras/formit/index) and [Login](https://docs.modx.com/current/en/extras/login/index) (specifically the [Register](https://docs.modx.com/current/en/extras/login/login.register) snippet).

The provided MODX snippet ***akismet*** is used as a *[hook](https://docs.modx.com/3.x/en/extras/formit/formit.hooks)* with FormIt, and a *[preHook](https://docs.modx.com/3.x/en/extras/login/login.tutorials/using-pre-and-post-hooks)* with Register.

Installation
-
Install Akismet via the [modmore package provider](https://modmore.com/about/package-provider/). Sign up for an Akismet account [here](https://akismet.com/plans/), then copy and paste the provided *API Key* into the new `akismet.api_key` system setting.  


Usage with FormIt
-
Within your FormIt snippet call, add `akismet` as one of your hooks. Preferably the first one, as to prevent other hooks running if spam is detected.

```
[[!FormIt? 
    &hooks=`akismet,email,redirect`
    ...
]]
```

Usage with Login
-
Within your Register snippet call, add `akismet` as one of your *preHooks*.

```
[[!Register?
    &preHooks=`akismet`
    ...
]]
```

Configurable Fields
-
Since Akismet was originally developed for Wordpress, it accepts fields that are related to comments on blog posts, 
such as `comment_author`, `comment_author_email` and `comment_content`.

MODX allows any naming convention for fields, so you can set the field names you're using as snippet parameters. This works with both FormIt and Register.

Say for example, you have a contact form with the following fields: `name`, `email` and `message`. 
You can set these to the fields that the Akismet service is expecting. See this code example:

```
[[!FormIt? 
    &hooks=`akismet,email,redirect`
    &akismetAuthor=`name`
    &akismetAuthorEmail=`email`
    &akismetContent=`message`
    ...
]]
```

**Complete List of Parameters**

- `&akismetAuthor` - The author's name.
- `&akismetAuthorEmail` - The author's email.
- `&akismetAuthorUrl` - The author's URL if they provided one.
- `&akismetContent` - The message content.
- `&akismentType` - The type of form submitted. Available types include *comment*, *forum-post*, *reply*, *blog-post*, *contact-form*, *signup*, *message*, and more. Read more [here](https://blog.akismet.com/2012/06/19/pro-tip-tell-us-your-comment_type/).
- `&akismetUserRole` - The type of user e.g. *visitor*, or *member*. If set to *Administrator*, the form will never be blocked.
- `&akismetTest` - Set this to `1` while developing so the AI knows it is just a test submission.
- `&akismetHoneypotField` - If you use a hidden honeypot field in your form, set the name of it here.
- `&akismetRecheckReason` - If you have a form where the same submission needs to be checked more than once, include the reason for it here.