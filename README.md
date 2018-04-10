# AdvancedReporterWebGui
It is a website implementation for the AdvancedReporter minecraft plugin with wich you can manage, create add a new reports via the gui.

# Installation
### 1. First Step
Upload all your files without the **database.sql** to your web host. For example upload your files in the **htdocs** folder if you are using **xampp**
***
### 2. Second Step
Change the **htaccess.txt** name to **.htaccess** and after that go to **inc/database.php** and change to following things to:
```php
$this->_dbhost = "your database host name";
$this->_dbuser = "your database username";
$this->_dbpass = "your database password";
$this->_dbname = "your database name";
```
***
### 3. Third Step
Open up the **phpmyadmin** and then edit the **database.sql** and change the following things:
```sql
INSERT INTO settings (name, type, value) VALUES ('website_scheme', 'string', 'http://');
INSERT INTO settings (name, type, value) VALUES ('website_domain', 'string', 'localhost');
INSERT INTO settings (name, type, value) VALUES ('table_name', 'string', 'advancedreporterreports');
```
1. If your website has **sql certificate** please change the **http://** to **https://**
2. Please change the **localhost** to your domain name what you are using for the website.
3. The third row is for the AdvancedReporter's table name. If you leaved it on default on the plugin's config don't change it.
***
### 4. Fourth Step
After you are done with the editing import the **database.sql** file to your database via **phpmyadmin**
***
### 5. Fifth Step
Now you can login with the following credentials:
##### **Email:** _demo@example.com_
##### **Password:** _demo123_
And you can how change your account details in the **Settings** menu point. P.S: for the **IGN** use your in-game name :)

# Plugin Information

##### _Description:_ An advanced plugin to manage the reports in your server. Staffer are warned for every new report and when they join. With this plugin you have a custom GUI, you can have sections and subsections and staffers can, with simple commands, manage and resolve reports (only this plugin can do this).
##### _Author:_ [Nexgan](https://www.spigotmc.org/members/nexgan.157889/)

# Task
_There are no tasks for now but you can suggest new things :)_
