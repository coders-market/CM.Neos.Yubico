Two factors authentication for Neos
========================================================

Neos package that integrates One Time Password (OTP) from [Yubico](https://www.yubico.com/). 
	
Features
--------

* Provide an authentication provider and token to support Yubikey OTP validation
* Self provisioning of Yubikey, the first time a use login with a Yubikey, the key is attached to the user
* Replace the Neos login screen to a third field for the Yubikey Token

![A Neos login box with OTP support](https://dl.dropboxusercontent.com/s/pi53fniqr0xuqiy/2015-01-21%20at%2001.23%202x.png?dl=0)

Requirements / Limitations
--------------------------

* You need to have a key that support OTP, this package is tested with a [Yubikey Neo](https://www.yubico.com/products/yubikey-hardware/)
* You can mix account with and without Yubikey
* A Yubikey can only be used for one single account

License
-------

Licensed under GPLv3+, see [LICENSE](LICENSE)