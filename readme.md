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

**Result**
Made it as a simple one file app, since there are no thoughts of scaling, so kept the design and everything simple. I'm not sure if storing this kind of data in json file is a good practice, but I chose this way, as it seemed simple enought to change the data and to add additional things if needed. Didn't have much time because of upcoming wedding, made the app just enough to be functional and cover main validations, errors. Even though, email validation is very primitive and covers just the basic functionality - could be improved to check MX records of submitted domain or something like that. Also, since I'm not familiar with Unit tests in practice yet, I didn't cover that thing. If I had more time, I would learn enough of it and added additional tests. What I don't like about my code is the handling of arguments, since it's kind of messy, I think there must be another way of elegantly covering those.

That's it for now. All the commands can be seen with 'help' argument.