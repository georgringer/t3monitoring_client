TYPO3 CMS Extension "t3monitoring_client"
=========================================
This extensions provides a client for the the extension **t3monitoring**.

**Requirements**

- TYPO3 CMS 9.5 LTS, 10.4 LTS or 11.3+ and 12.4
- Use the branch `4-5` for support from 4.5 - 6.1
- Use the branch `7-8` for support from 7.0 - 8.7

**Sponsors**

- Sup7
- Reelworx

**Important**

This extension is still beta and things might change!

Configuration
-------------
After installing the extension, configure the extension in the settings of the *Extension Manager*.

Secret
""""""
Define any secret you want as long as it is at least 5 chars long. Ideally you use a different secret per installation.

allowedIps
""""""""""
Define a comma separated list of IPs which are allowed to fetch the client data.

enableDebugForErrors
""""""""""""""""""""
If set, the errors are outputted if you call `http://yourdomain.tld/?eID=t3monitoring&secret=<yoursecret>`. This can help to identify problems.

**Important**: Turn this setting only on for testing and disable it afterwards!

Extending the client
--------------------

It is possible to extend the client and provide additional data which can be displayed later in the master installation.

Things might change there, so no manual about it yet.
