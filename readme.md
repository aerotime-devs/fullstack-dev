**How-To**
- Fork this repo.
- Complete task fulfilling requirements.
- Prepare pull request and let us know. 


**Fullstack Php Developer Task**

Let’s say we have a business requirement to register a Lead. The customer's desire to import data to the lead-register using CSV files.

The csv file has following fields:

firstname;lastname;email;phonenumber1;phonenumber2;comment;

**The application should have following functionality:**
- Register a new lead
- Delete a person from the lead-register
- Find a person in the lead-register
- Import of persons using a CSV-file

**Potential problem areas to think about:**
- Duplicated persons
- Data validation (email)

**Requirements are:**
- All commands are executed from CLI
- Application should not use any framework
- Include at least 3 unit tests
- Application Data should be stored in file
- Provide brief information what wasn’t implemented and what can be improved

## Tested with Ubuntu 20

 - php 7.4.+
 - [composer](https://getcomposer.org/) 

## Installing

```
composer install
```

## Run PHP script

### All commands
```
php run.php
```
```
Available commands:
  deletePersonFromLeadRegisterByEmail  Delete a person from the lead-register by Email.
  findPersonInLeadRegister             Find a person in the lead-register.
  help                                 Displays help for a command
  importPersonsUsingCsvFile            Import of persons using a CSV-file.
  list                                 Lists commands
  registerNewLead                      Register a new lead.
```

 #### Register a new lead example.

```
php run.php registerNewLead Vardenis Pavardenis vardenis@email.lt 860000000 86999999 "Nice cooment"
```

 #### Find a person in the lead-register by first and last name example.

```
php run.php findPersonInLeadRegister Vardenis Pavardenis
```

 #### Delete a person from the lead-register by Email example.

```
php run.php deletePersonFromLeadRegisterByEmail vardenis@email.lt
```

 #### Import of persons using a CSV-file example.

```
php run.php importPersonsUsingCsvFile test.csv
```

## Run phpUnit tests
```
./vendor/bin/phpunit
```

##**Requirements are:**

- All commands are executed from CLI
- Application should not use any framework
- Include at least 3 unit tests
- Application Data should be stored in file
- Provide brief information what wasn’t implemented and what can be improved:

####**What can be improved:**
- **PhpUnit test**

####Potential problem areas to think about:

- Duplicated persons
    - **Duplicated persons not added in JSON file**
- Data validation (email)
    - **Added email validation from csv**
    - **Added email validation "Register a new lead"**
