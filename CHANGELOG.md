CHANGELOG
==============

1.2.0
-----------------
  * security: the redirect page accepts only absolute http(s) links, other values get 400
  * security: escaped output of the link in HTML and JS (reflected XSS)
  * security: replaced links are signed (HMAC, cookieValidationKey); unsigned or wrongly signed links are not opened automatically, the page shows a confirmation button
  * redirect page is sent with noindex, nofollow (X-Robots-Tag and meta)
  * html entities in replaced hrefs are decoded before encoding the link

1.1.1
-----------------
  * replacing references only after <body

1.1.0
-----------------
  * remove callback property
  * EVENT_BEFORE_PROCESSING before process replace
  * fix bug whith redirects

1.0.1
-----------------
  * fix bug whith redirects

1.0.0
-----------------
  * Stable release
