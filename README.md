# Submission Generator
## Introduction

This README guide explicates the steps required to execute this PHP CLI script.

### Prerequisites
Ensure that PHP is installed on your machine. You can confirm this by running php -v in your terminal/command prompt.
The script utilizes packages, thus, make certain that composer is installed on your machine. Once composer is installed, navigate to your script directory and run composer install to install the required packages.
Retrieve the script (download or clone the repository it is contained in).
### Instructions to Run The Script
Open your terminal/command prompt.
Move into the directory holding the script.
Execute the script by typing the necessary command.
Example

The following command executes the script with default parameters:

```php
php generate_submissions.php
```

The script handles two options: student-count and batch-size. Here is how to use these options:

php script.php --student-count=30 --batch-size=15


Note that the batch-size value cannot exceed 50.

If you want more assistance, you can use the help option as shown in the example:

php script.php --help

Error management

If errors occur, reassess that you have installed the right PHP version and required packages. For issues directly tied to the script, please submit an issue.

Contributing

Contributions are welcome! Fork the project and submit a pull request with your alterations!

Make sure your code passes all tests before attempting a pull request.

NB: This guide is generic and certain information might differ based on the specific script.