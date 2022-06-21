<img src="https://ppnt.poznan.pl/wp-content/uploads/2016/04/panda_group-270x59.jpg)" alt="Panda Group logo" width="300"/>
<img src="https://www.subuno.com/wp-content/uploads/2019/04/cropped-image10-4.png" alt="Subuno Fraud Prevention Logo" width="300"/>

## MAGENTO 2 SUBUNO FRAUD PREVENTION MODULE

Magento 2 Subuno Fraud Prevention Module detects risky purchases and sends all data back to Magento.

### About

---

**For whom?**<br>
If you are a Magento 2 online store owner, then Magento 2 Subuno's Fraud Prevention Module is a must-have for your platform. This Magento 2 extension enables seamless communication between the e-commerce site and Subuno API provider so that potentially risky purchases can be automatically detected before they're shipped off to their destination!

**Anti-fraud solution**<br>
**Magento 2 Subuno Fraud Prevention Module** is a solution for e-commerce platforms to automatically review orders and marked potential risky purchases as possible fraud. This Magento 2 module connects your online store with Subuno API, sending all data back to Magento so you can take the necessary precautions before shipping anything out!

**About Authors**<br>
[Panda Group Magento Agency](https://pandagroup.co/) is a team of professionals who specialise in custom-built eCommerce solutions for companies looking to take their business into the future.
<br>
With our stable and reliable technology stack, you can rest assured that your site will not suffer from technological issues like downtime or glitches as it grows with demand!

* [Panda Know-How](https://pandagroup.co/blog)    
* [Panda Group Services](https://pandagroup.co/services)
* [Panda Group Magento Agency Case Study](https://pandagroup.co/case-study)


### Docs

---

1. Usage

[Subuno Fraud Prevention](https://www.subuno.com/) is a solution for e-commerce platforms to automatically review orders and marked potential risky purchases as a fraud. This module is a Magento 2 Module that helps connect online store with Subuno API. Orders are sent to Subuno and the information about review is sent back to Magento.

2. Installation

To your composer.json file add configuration about custom repository:
```
"repositories": {
    "pandagroup/subuno-php-api": {
            "type": "vcs",
            "url": "git@bitbucket.org:l4w/subuno-php-api.git"
    },
    "pandagroup/magento2-pandagroup-subuno": {
            "type": "vcs",
            "url": "git@bitbucket.org:l4w/magento2-pandagroup-subuno.git"
    }
}
```

As you can see, Magento 2 module has dependencies on SDK pandagroup/subuno-php-api - it also has to be configured and installed via Composer. When the config in composer.json is ready, run
```
composer require pandagroup/magento2-pandagroup-subuno:^1
```

3. Configuration

Running Subuno Fraud Prevention module on Magento 2 requires to have valid account in Subuno. Navigate to 
> Stores > Configuration > Panda Group > Subuno > Connection Settings

And paste in your API key from the Subuno account. Remember also to enable module in settings: 
> Stores > Configuration > Panda Group > Subuno > General

There are several ways to configure Subuno in Magento 2:
<br>-> When to run Subuno Fraud Prevention: 
* <i>Run Subuno check during checkout</i> - means: call the Subuno API during checkout and payment process
* <i>Run Subuno check asynchronously</i> - means: call the Subuno API after transaction through cron job

<br>-> Action after subuno reject:
* <i>Put the order on hold</i>
* <i>Cancel the order</i>
* <i>Throw error and do not save the transaction</i>

In case of the third option with throwing an error, remember that this error will be shown to the customer. You can edit it in a field <i>Message for customer when Subuno validation will failed</i>