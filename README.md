# Payssion Magento2 plugin

---
- [Quickstart](#quickstart)
- [Setup](#setup)

---


### Quickstart

#####Installing

1. Upload content of module to magento2 root folder

2. In command line, navigate to the magento2 root folder
Enter the following commands:

```
php bin/magento module:enable Payssion_Payment
php bin/magento setup:upgrade
php bin/magento cache:clean
```

The plugin is now installed

#####Setup

1. Log into the Magento Admin
2. Go to *Stores* / *Configuration*
3. Go to *Sales* / *Payment Methods*
4. Find the Payssion Settings, enter the API Key and Secret Key
5. Enable the desired payment methods and set allowed countries
6. Save the settings
