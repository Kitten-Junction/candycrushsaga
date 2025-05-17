# 🍬 Candy Crush Saga

![Discord](https://img.shields.io/discord/855650531354476554?style=social&logo=discord&link=https%3A%2F%2Fdiscord.gg%2FUTkJ5zE6wX) ![Our Bluesky](https://img.shields.io/badge/Bluesky_Page-brightgreen?style=social&logo=bluesky&link=https%3A%2F%2Fbsky.app%2Fprofile%2Fspiritaccount.net) ![Supported](https://img.shields.io/badge/1.88-brightgreen?style=social&logo=android)

A recreation of the original CCS servers using Spirit Account.

The root of the project contains an pre-patched v1.88 Candy Crush APK pointed to SA CCS servers, use it to do anything you want.

Inspired by Ender's candyflashserver!

## Features:

- Flash <> Android (V1.88) sync
- Kingdom account data migration
- Paid Booster Wheel
- Life sending
- Friends (aka Social Features)
- Automatic language detection
- Dreamworld
- Synergie Bonus
- Mobile connection reward
- Recreated JSON-RPC server

## Credits

- ender_pearl for helping with API responses
- thegreenspirit for helping with Kingdom systems, frontend and making the CCS Product Manager
- MJN/Dalton and Spirit Account playtesters for testing the game!
- Daphne for improving levels
- Mystic for helping with translations
- idkwhattoput for putting hard work on this project since beginning!

## Setup

> [!IMPORTANT]  
> Unlike `candyflashserver` made by Ender which uses Kingdom mode and Kingdom account data directly, this project relies heavily on Facebook integration, Because of this, **a valid Facebook App is required to play the game**.
> 
> However, since Facebook for Developers is a really questionable thing, we use Spirit Account servers to handle social and login functionalities, Despite this, a valid Facebook App ID (**Spirit Account App ID** in this case) and App Secret are still necessary for server setup.
> 
> if you want an experimental app please **do not hesitate to contact us** at spiritaccount@chiyochan.shop or on the [The Green Spirit Discord server](https://discord.gg/UTkJ5zE6wX)
> 
> You can also open an issue about it here.
> 
> Once you have got in hands of an valid App ID and secret, replace the ones on `common.php` to the ones you just got and done!

To create an local instance of CCS, you need: 

- A fully working HTTP server that supports PHP 
- A valid and active Spirit Account app (Explained above)
- A MySQL database with the DB.sql (On backend folder) data injected 
- An virtualhost configuration where it's `DocumentRoot` (or `root`) is pointed to the `Frontend` folder.
- A secondary virtualhost located on the same server that has write permission. (will be used for profile picture CDN)

For the HTTP server, both Apache2 and NGINX should work fine, but take note that if you are using NGINX you will need to covert `.htaccess` rules.

If you are lazy and don't want to install the dependencies manually, you can use XAMPP or WampServer

Edit the database details on database.php so it matches yours!

> [!NOTE]  
> Be sure to check the data saving paths and edit them correctly, you can find them by searching `/var/www/candycrush/` using Notepad++ or any other editing software that let's you search an term in multiple directories.

Finally, just go to https://spiritaccount.net/APPNAMESPACE/ replacing APPNAMESPACE with your CCS app namespace!

## Mobile Setup

If you want to play CCS in your mobile device using this server, you need to meet these requirements first:

- Server is accessible in your mobile device
- Patched Spirit Account URLs via MT Manager or another tool (`facebook.com` find, replace with `spiritaccount.net`)
- Edited the CCS server URL on `/assets/res_output/socialproperties_live.prop` (Located inside APK)