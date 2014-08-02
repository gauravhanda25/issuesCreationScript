Command Line Script to create gitHub and BitBucket issues using APIs
===========================================================

This project contains a Php script that can be used to create GitHub and BitBucket issues by calling the APIs provided by GitHub and BitBucket respectively. The script can be extended to include other repositories as well by creating a class and a function to post issue for that repository and can also be extended to include other functions such as get posts, delete posts, update posts etc.


Setup Instructions
------------------

1. Create a php script and execute it using command prompt. In command prompt change the directory to reach the root folder of Php in wamp directory where php.exe file is present. Alternatively, if the environmental variable for php is set in the system then this is not required. Php.exe file is normally present in 
**\<Drive in which wamp is installed>\wamp\bin\php\\\<phpversion>** eg: **C:\wamp\bin\php\php5.3.8**
 
2. Use the following command to execute the script
	2.a) For windows
		**php.exe -f <php script path> username \<username> password \<password> <repository URL> <"Issue Title"> <"Steps to reproduce the problem">**
		eg: **php.exe -f C:/Users/Joe/Desktop/createIssue.php username joe password secret https://github.com/example/test "My Issue Title" "These are the steps required"**.
	2.b) For Linux(Ubuntu)	
		 php -f <php script path> username \<username> password \<password> <repository URL> <"Issue Title"> <"Steps to reproduce the problem">**
		eg: **php -f var/www/issues/createIssue.php username joe password secret https://github.com/example/test "My Issue Title" "These are the steps required"**.
Script can also be executed by using the command : 
**php.exe -f <php script path>**
after step 1 and then manually entering the input parameters from the command line.

**NOTE:** This script uses CURL to connect to APIs, Please uncomment the **extension=php_curl.dll** extension in the php.ini file for windows and/or use the following commands in Linux(Ubuntu)
**sudo apt-get install php5-curl**
**sudo /etc/init.d/apache2 restart**
to enable this script to execute
