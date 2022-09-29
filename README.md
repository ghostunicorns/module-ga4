# Description

This module adjust the GTM pushes to be compatible with the new GA4 ecommerce standards 

# Install

`composer require ghost-unicorns/module-ga4`

# How to configure
Make sure that Google Analytics 4 through Google GTag is enabled 
`Stores -> Configuration -> Sales -> Google APIS -> Google GTag -> Google Analytics4 -> Enable -> Yes`

Make sure that Google Analytics 4 is injected via GTM
`Stores -> Configuration -> Sales -> Google APIS -> Google GTag -> Google Analytics4 -> Account type -> Google Tag Manager`

Make sure that Google Analytics through Google Analytics standard is disabled
`Stores -> Configuration -> Sales -> Google Analytics -> Enable -> No`


# Customize
You can create a plugin on the following file to change the products data:
`GhostUnicorns\Ga4\Model\GetProductLayer`

# Contribution

Yes, of course you can contribute sending a pull request to propose improvements and fixes.

