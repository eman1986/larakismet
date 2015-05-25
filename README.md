# Larakismet

Akismet Client for Laravel 5.

#Installation

simply include this library to your app's composer.json file in the request block:

    "require": {
        "eman1986/larakismet": "dev-master",
    }

Once you have the package loaded into your application's file system, open the config/app.php file and add the following line to the 'providers' array:

    'larakismet\AkismetServiceProvider'

run the following command in your terminal:

    php artisan vendor:publish

This will create a config file for you where you can enter in the API Key (which if you don't have one, you'll need one, visit https://akismet.com)
and enter in the address of your blog. You can also setup a debug mode to just test out the akismet API.

#What's Next?

After everything is all configured, you can now use the code in your application.

##checkSpam()

This will allow you to run a check on a comment post and ensure its not spam.

Akismet likes to have as much information as possible to properly determine if something is indeed Spam.

 If you were to read the Akismet API on this, they ask for a lot of things but at a minimal you'll need the following set:

    larakismet->setCommentAuthor('John Doe');
    larakismet->setCommentAuthorEmail('email@example.com');
    larakismet->setPermalink('http://somesite.com/blog/sample-entry');
    larakismet->setCommentContent('Some content from form.');
    larakismet->checkSpam();

If you check out the source code you can see the other options available to zero in on the spammer, the Akismet API Guide is also a good reference..

##reportSpam()

You can help Akismet tackle spam by reporting it to them, this requires a smaller set of dat compared to the checkSpam() method.

    larakismet->setCommentAuthor('John Doe');
    larakismet->setCommentAuthorEmail('email@example.com');
    larakismet->setPermalink('http://somesite.com/blog/sample-entry');
    larakismet->setCommentContent('Some content from form.');
    larakismet->reportSpam();

##reportHam()

You can also report false positives to Akismet by doing the following:

    larakismet->setCommentAuthor('John Doe');
    larakismet->setCommentAuthorEmail('email@example.com');
    larakismet->setPermalink('http://somesite.com/blog/sample-entry');
    larakismet->setCommentContent('Some content from form.');
    larakismet->reportHam();
    
ReportSpam() & reportHam() will accept the same parameters. Using these two methods will help make the web a better place for all of us.

#Questions?

If you need help, please let me know and I'll be happy to assist.